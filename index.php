<?php
date_default_timezone_set('Africa/Cairo');
session_start();
require_once __DIR__ . '/includes/audit.php';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("location: ./modules/dashboard.php");
    exit();
}

$error        = "";
$signup_error = "";
$signup_ok    = "";

$connect = new mysqli("localhost", "root", "", "hms");
if ($connect->connect_error) die("Connection failed: " . $connect->connect_error);

// ── SIGN IN ──────────────────────────────────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $role       = $_POST['role']       ?? '';
    $password   = $_POST['password']   ?? '';
    $identifier = trim($_POST['identifier'] ?? '');

    if ($role === 'Patient') {
        $stmt = $connect->prepare("SELECT * FROM `users` WHERE `nat_id` = ? AND `password` = ?");
        $stmt->bind_param("ss", $identifier, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            session_regenerate_id(true);
            $_SESSION['logged_in'] = true;
            $_SESSION['role']      = 'Patient';
            $_SESSION['login']     = $identifier;
            $_SESSION['uid']       = $row['uid'];
            $_SESSION['username']  = $row['fullName'];
            hms_audit_log($connect, 'auth.login.success', [
                'entity_type' => 'auth',
                'entity_id' => 'patient',
                'description' => 'Successful login',
                'details' => [
                    'role' => 'Patient',
                    'identifier' => $identifier,
                ],
            ]);
            header("location: ./modules/dashboard.php"); exit();
        } else {
            hms_audit_log($connect, 'auth.login.failed', [
                'entity_type' => 'auth',
                'entity_id' => 'patient',
                'description' => 'Failed login attempt',
                'details' => [
                    'role' => 'Patient',
                    'identifier' => $identifier,
                ],
            ]);
            $error = "National ID or Password is incorrect.";
        }

    } elseif ($role === 'Doctor') {
        $stmt = $connect->prepare("SELECT * FROM `doctors` WHERE `docEmail` = ? AND `password` = ?");
        $stmt->bind_param("ss", $identifier, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            session_regenerate_id(true);
            $_SESSION['logged_in'] = true;
            $_SESSION['role']      = 'Doctor';
            $_SESSION['login']     = $identifier;
            $_SESSION['id']        = $row['id'];
            $_SESSION['username']  = $row['doctorName'];
            $connect->query("UPDATE doctors SET statue = 1 WHERE id = " . intval($row['id']));
            hms_audit_log($connect, 'auth.login.success', [
                'entity_type' => 'auth',
                'entity_id' => 'doctor',
                'description' => 'Successful login',
                'details' => [
                    'role' => 'Doctor',
                    'identifier' => $identifier,
                ],
            ]);
            header("location: ./modules/dashboard.php"); exit();
        } else {
            hms_audit_log($connect, 'auth.login.failed', [
                'entity_type' => 'auth',
                'entity_id' => 'doctor',
                'description' => 'Failed login attempt',
                'details' => [
                    'role' => 'Doctor',
                    'identifier' => $identifier,
                ],
            ]);
            $error = "Email or Password is incorrect.";
        }

    } elseif (in_array($role, ['Admin', 'System Admin', 'User'])) {
        $stmt = $connect->prepare("SELECT * FROM `employ` WHERE `email` = ? AND `password` = ? AND `role` = ?");
        $stmt->bind_param("sss", $identifier, $password, $role);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row  = $result->fetch_assoc();
            $date = date("Y-m-d");
            session_regenerate_id(true);
            $_SESSION['logged_in'] = true;
            $_SESSION['role']      = $row['role'];
            $_SESSION['login']     = $identifier;
            $_SESSION['id']        = $row['id'];
            $_SESSION['username']  = $row['username'];
            $connect->query("UPDATE employ SET employ_statue = 1 , updationDate = '$date' WHERE id = " .$row['id']);
            hms_audit_log($connect, 'auth.login.success', [
                'entity_type' => 'auth',
                'entity_id' => strtolower(str_replace(' ', '-', $row['role'])),
                'description' => 'Successful login',
                'details' => [
                    'role' => $row['role'],
                    'identifier' => $identifier,
                ],
            ]);
            header("location: ./modules/dashboard.php");
            exit();
        } else {
            hms_audit_log($connect, 'auth.login.failed', [
                'entity_type' => 'auth',
                'entity_id' => strtolower(str_replace(' ', '-', $role ?: 'employee')),
                'description' => 'Failed login attempt',
                'details' => [
                    'role' => $role,
                    'identifier' => $identifier,
                ],
            ]);
            $error = "Email or Password is incorrect.";
        }
    } else {
        hms_audit_log($connect, 'auth.login.invalid_role', [
            'entity_type' => 'auth',
            'entity_id' => 'login',
            'description' => 'Login submitted without a valid role',
            'details' => [
                'identifier' => $identifier,
                'role' => $role,
            ],
        ]);
        $error = "Please select your role.";
    }
}

// ── SIGN UP (Patient only) ────────────────────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'register') {
    $fullName  = trim($_POST['fullName']   ?? '');
    $email     = trim($_POST['reg_email']  ?? '');
    $nat_id    = trim($_POST['nat_id']     ?? '');
    $pass1     = $_POST['reg_pass']        ?? '';
    $pass2     = $_POST['reg_pass2']       ?? '';
    $gender    = trim($_POST['gender']     ?? '');
    $age       = intval($_POST['age']      ?? 0);

    if ($fullName === '' || $email === '' || $nat_id === '') {
        $signup_error = "Please fill all required fields.";
    } elseif ($pass1 !== $pass2) {
        $signup_error = "Passwords do not match.";
    } elseif (strlen($pass1) < 6) {
        $signup_error = "Password must be at least 6 characters.";
    } else {
        // Check whether email already belongs to another record
        $chkEmail = $connect->prepare("SELECT uid, nat_id, email, password FROM `users` WHERE `email` = ? LIMIT 1");
        $chkEmail->bind_param("s", $email);
        $chkEmail->execute();
        $emailRes = $chkEmail->get_result();
        $emailRow = $emailRes->fetch_assoc();
        $chkEmail->close();

        // Check whether national ID already exists
        $chkNat = $connect->prepare("SELECT uid, fullName, nat_id, email, password, PatientContno, gender, p_age FROM `users` WHERE `nat_id` = ? LIMIT 1");
        $chkNat->bind_param("s", $nat_id);
        $chkNat->execute();
        $natRes = $chkNat->get_result();
        $natRow = $natRes->fetch_assoc();
        $chkNat->close();

        // If email exists in another different record, reject
        if ($emailRow && (!$natRow || intval($emailRow['uid']) !== intval($natRow['uid']))) {
            $signup_error = "This email is already registered.";
        } else {
            $newPassword = $pass1;

            if ($natRow) {
                $existingUid      = intval($natRow['uid']);
                $existingEmail    = trim($natRow['email'] ?? '');
                $existingPassword = trim($natRow['password'] ?? '');

                // If record already has a real account, reject new registration
                if ($existingEmail !== '' && $existingPassword !== '') {
                    $signup_error = "This National ID is already registered. Please sign in.";
                } else {
                    // Existing hospital-created patient record: upgrade it to a real account
                    $upd = $connect->prepare("
                        UPDATE `users`
                        SET `fullName` = ?,
                            `email`    = ?,
                            `password` = ?,
                            `gender`   = CASE WHEN ? <> '' THEN ? ELSE `gender` END,
                            `p_age`    = CASE WHEN ? > 0 THEN ? ELSE `p_age` END
                        WHERE `uid` = ?
                    ");
                    $upd->bind_param(
                        "sssssiii",
                        $fullName,
                        $email,
                        $newPassword,
                        $gender,
                        $gender,
                        $age,
                        $age,
                        $existingUid
                    );

                    if ($upd->execute()) {
                        hms_audit_log($connect, 'auth.register.success', [
                            'entity_type' => 'user',
                            'entity_id' => (string)$existingUid,
                            'description' => 'Existing patient account activated',
                            'details' => [
                                'nat_id' => $nat_id,
                                'email' => $email,
                            ],
                        ]);
                        $signup_ok = "Account activated successfully! You can now sign in.";
                    } else {
                        hms_audit_log($connect, 'auth.register.failed', [
                            'entity_type' => 'user',
                            'entity_id' => (string)$existingUid,
                            'description' => 'Failed to activate existing patient account',
                            'details' => [
                                'nat_id' => $nat_id,
                                'email' => $email,
                            ],
                        ]);
                        $signup_error = "Failed to activate existing patient record.";
                    }
                    $upd->close();
                }
            } else {
                // Completely new patient
                $ins = $connect->prepare("
                    INSERT INTO `users`
                    (`fullName`,`email`,`nat_id`,`password`,`gender`,`p_age`,`PatientContno`,`PatientMedhis`)
                    VALUES (?,?,?,?,?,?,0,'')
                ");
                $ins->bind_param("sssssi", $fullName, $email, $nat_id, $newPassword, $gender, $age);

                if ($ins->execute()) {
                    $newUserId = $connect->insert_id;
                    hms_audit_log($connect, 'auth.register.success', [
                        'entity_type' => 'user',
                        'entity_id' => (string)$newUserId,
                        'description' => 'New patient account created',
                        'details' => [
                            'nat_id' => $nat_id,
                            'email' => $email,
                        ],
                    ]);
                    $signup_ok = "Account created! You can now sign in.";
                } else {
                    hms_audit_log($connect, 'auth.register.failed', [
                        'entity_type' => 'user',
                        'entity_id' => 'new',
                        'description' => 'Failed to create patient account',
                        'details' => [
                            'nat_id' => $nat_id,
                            'email' => $email,
                        ],
                    ]);
                    $signup_error = "Registration failed. Please try again.";
                }
                $ins->close();
            }
        }
    }
}

$sel = $_POST['role'] ?? 'Patient';
$showRegister = isset($_POST['action']) && $_POST['action'] === 'register';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HMS - Portal</title>
<link rel="icon" href="./assets/images/echol.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}

:root {
  --blue:    #2d7dd2;
  --blue2:   #1a5fad;
  --light:   #f0f4f8;
  --muted:   #8a9ab5;
  --dark:    #1c2637;
  --white:   #ffffff;
  --panel-w: 42%;
  --radius:  28px;
  --speed:   0.65s;
}

body {
  font-family: 'DM Sans', sans-serif;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #dde8f5;
  background: radial-gradient(ellipse at 20% 80%, #c5d8f0 0%, #dde8f5 50%, #e8eef7 100%);
  padding: 20px;
}

/* ── WRAPPER ── */
.wrapper {
  position: relative;
  width: 960px;
  max-width: 100%;
  height: 620px;
  background: var(--white);
  border-radius: var(--radius);
  box-shadow: 0 32px 80px rgba(28,38,55,0.18), 0 8px 24px rgba(28,38,55,0.08);
  overflow: hidden;
}

/*
  ANIMATION STRATEGY:
  - Panel   : absolute, left:0,   width:42%  → slides RIGHT by 58% of WRAPPER
  - signin  : absolute, left:42%, width:58%  → slides LEFT  by 58% of WRAPPER
  - signup  : absolute, right:0,  width:58%  → starts translated right, slides to 0

  KEY: use vw or px won't work — we use a CSS custom property set by JS
  to avoid % ambiguity (% in translateX = % of element itself, not wrapper)
*/

/* ── PANEL ── */
.panel {
  position: absolute;
  top: 0; left: 0;
  width: 42%;
  height: 100%;
  border-radius: var(--radius) 0 0 var(--radius);
  overflow: hidden;
  transition: left var(--speed) cubic-bezier(.77,0,.18,1),
              border-radius var(--speed) cubic-bezier(.77,0,.18,1);
  z-index: 10;
}
.wrapper.active .panel {
  left: 58%;
  border-radius: 0 var(--radius) var(--radius) 0;
}

/* ── FORMS ── */
.form-box {
  position: absolute;
  top: 0;
  height: 100%;
  width: 58%;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 50px;
  transition: left var(--speed) cubic-bezier(.77,0,.18,1),
              opacity calc(var(--speed)*0.4) ease;
  z-index: 1;
}

/* signin: sits on the right, visible */
.form-box.signin {
  left: 42%;
  opacity: 1;
  pointer-events: all;
}

/* signup: hidden off the right edge */
.form-box.signup {
  left: 100%;
  opacity: 0;
  pointer-events: none;
}

/* ACTIVE */
.wrapper.active .form-box.signin {
  left: -58%;
  opacity: 0;
  pointer-events: none;
}
.wrapper.active .form-box.signup {
  left: 0;
  opacity: 1;
  pointer-events: all;
}

.panel-img {
  position: absolute;
  inset: 0;
  background: url('./assets/images/patient-landing/doctor.jpg') center/cover no-repeat,
              url('./assets/images/doctor_2732252.png') center/cover no-repeat;
  filter: brightness(.75) saturate(1.1);
}

.panel-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(160deg,
    rgba(13,30,70,0.55) 0%,
    rgba(29,95,180,0.4) 60%,
    rgba(13,30,70,0.7) 100%);
}

.panel-content {
  position: relative;
  z-index: 1;
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 36px 32px;
  color: #fff;
}

.panel-logo {
  display: flex;
  align-items: center;
  gap: 10px;
}
.panel-logo img { width: 36px; height: 36px; object-fit: contain; filter: brightness(10); }
.panel-logo span { font-family: 'Playfair Display', serif; font-size: 17px; font-weight: 700; letter-spacing: .3px; }

.panel-main { text-align: center; }
.panel-main h2 {
  font-family: 'Playfair Display', serif;
  font-size: 30px;
  font-weight: 700;
  line-height: 1.2;
  margin-bottom: 12px;
  text-shadow: 0 2px 12px rgba(0,0,0,.3);
}
.panel-main p {
  font-size: 13.5px;
  color: rgba(255,255,255,.75);
  line-height: 1.6;
  margin-bottom: 28px;
}

.panel-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 11px 28px;
  border: 2px solid rgba(255,255,255,.8);
  border-radius: 50px;
  color: #fff;
  font-size: 13px;
  font-weight: 600;
  letter-spacing: .8px;
  text-transform: uppercase;
  cursor: pointer;
  background: transparent;
  transition: background .25s, border-color .25s;
}
.panel-btn:hover { background: rgba(255,255,255,.15); border-color: #fff; }

.panel-footer { font-size: 11.5px; color: rgba(255,255,255,.4); text-align: center; }

/* ── FORM INNER ── */
.form-inner { width: 100%; max-width: 340px; }
.form-inner h1 {
  font-family: 'Playfair Display', serif;
  font-size: 28px;
  color: var(--dark);
  margin-bottom: 6px;
}
.form-inner .sub {
  font-size: 13px;
  color: var(--muted);
  margin-bottom: 24px;
}

/* Role pills */
.roles-label { font-size: 11px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: .6px; margin-bottom: 9px; }
.roles {
  display: flex;
  flex-wrap: wrap;
  gap: 7px;
  margin-bottom: 22px;
}
.role-btn {
  flex: 1 1 auto;
  min-width: 70px;
  padding: 8px 6px;
  border-radius: 10px;
  border: 1.5px solid #dce5f0;
  background: var(--light);
  color: var(--muted);
  font-size: 11.5px;
  font-weight: 600;
  cursor: pointer;
  transition: all .22s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
}
.role-btn i { font-size: 12px; }
.role-btn:hover { border-color: var(--blue); color: var(--blue); background: #edf4fc; }
.role-btn.active {
  border-color: var(--blue);
  background: linear-gradient(135deg,#eaf2fd,#d9eafa);
  color: var(--blue);
  box-shadow: 0 3px 10px rgba(45,125,210,.15);
}
.role-btn.super { flex: 1 1 100%; }

/* Fields */
.field { margin-bottom: 13px; position: relative; }
.field label { display: block; font-size: 11.5px; font-weight: 600; color: var(--dark); margin-bottom: 6px; letter-spacing: .3px; }
.field .ico { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 13px; pointer-events:none; }
.field input, .field select {
  width: 100%;
  padding: 11px 14px 11px 38px;
  background: var(--light);
  border: 1.5px solid #dce5f0;
  border-radius: 10px;
  color: var(--dark);
  font-size: 13.5px;
  font-family: 'DM Sans', sans-serif;
  outline: none;
  transition: border-color .2s, background .2s, box-shadow .2s;
  appearance: none;
}
.field input::placeholder { color: #b0bdd0; }
.field input:focus, .field select:focus {
  border-color: var(--blue);
  background: #fff;
  box-shadow: 0 0 0 3px rgba(45,125,210,.1);
}

/* Two-col fields */
.field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }

/* Submit */
.btn-submit {
  width: 100%; padding: 13px;
  background: linear-gradient(135deg, var(--blue), var(--blue2));
  border: none; border-radius: 11px;
  color: #fff; font-size: 14px; font-weight: 700;
  font-family: 'DM Sans', sans-serif;
  letter-spacing: .3px; cursor: pointer; margin-top: 6px;
  display: flex; align-items: center; justify-content: center; gap: 8px;
  transition: transform .15s, box-shadow .2s, opacity .2s;
  box-shadow: 0 6px 20px rgba(45,125,210,.3);
}
.btn-submit:hover  { transform: translateY(-1px); box-shadow: 0 10px 28px rgba(45,125,210,.38); }
.btn-submit:active { transform: translateY(0); }

/* Alert */
.alert {
  padding: 10px 13px; border-radius: 9px;
  font-size: 12.5px; margin-bottom: 14px;
  display: flex; align-items: center; gap: 8px;
}
.alert.err  { background:#fef0ef; border:1px solid #fbbdb9; color:#c0392b; }
.alert.ok   { background:#edfaf3; border:1px solid #a3e6c0; color:#1a7a4a; }

/* divider */
.divider { display:flex; align-items:center; gap:10px; margin:16px 0 12px; }
.divider span { font-size:11px; color:#c0cdd8; white-space:nowrap; }
.divider::before,.divider::after { content:''; flex:1; height:1px; background:#dce5f0; }

/* ── RESPONSIVE ── */
@media(max-width: 680px) {
  body { padding: 0; }
  .wrapper {
    height: auto;
    min-height: 100vh;
    border-radius: 0;
    width: 100%;
    overflow-x: hidden;
  }
  .panel { display: none; }
  .form-box {
    position: relative !important;
    left: 0 !important;
    width: 100% !important;
    transform: none !important;
    opacity: 1 !important;
    padding: 32px 20px;
    box-sizing: border-box;
  }
  .form-box.signup { display: none; pointer-events: none; }
  .wrapper.active .form-box.signin  { display: none; }
  .wrapper.active .form-box.signup  { display: flex; }
  .form-inner {
    max-width: 100% !important;
    width: 100% !important;
  }
  .roles {
    gap: 6px;
  }
  .role-btn {
    min-width: 0;
    padding: 8px 4px;
    font-size: 11px;
  }
  .role-btn.super {
    flex: 1 1 100%;
  }
  .field input, .field select {
    font-size: 14px;
    padding: 10px 12px 10px 36px;
  }
  .field-row {
    grid-template-columns: 1fr 1fr;
    gap: 8px;
  }
  .btn-submit {
    font-size: 13.5px;
    padding: 12px;
  }
  .form-inner h1 {
    font-size: 24px;
  }
}
</style>

<link rel="stylesheet" href="/assets/css/responsive.css">
</head>
<body>

<div class="wrapper <?= $showRegister ? 'active' : '' ?>" id="wrapper">

  <!-- ── IMAGE PANEL ── -->
  <div class="panel" id="panel">
    <div class="panel-img"></div>
    <div class="panel-overlay"></div>
    <div class="panel-content">

      <div class="panel-logo">
        <img src="./assets/images/echol.png" alt="" onerror="this.style.display='none'">
        <span>HMS Portal</span>
      </div>

      <div class="panel-main" id="panel-main">
        <h2 id="panel-title">Welcome<br>Back!</h2>
        <p id="panel-desc">Sign in with your credentials<br>to access your dashboard.</p>
        <button class="panel-btn" id="panel-cta" onclick="togglePanel()">
          <i class="fa fa-user-plus" id="panel-icon"></i>
          <span id="panel-cta-text">Register</span>
        </button>
      </div>

      <div class="panel-footer">Hospital Management System © 2025</div>
    </div>
  </div>

  <!-- ── SIGN IN FORM ── -->
  <div class="form-box signin">
    <div class="form-inner">
      <h1>Sign In</h1>
      <p class="sub">Select your role and enter your credentials</p>

      <?php if ($error): ?>
      <div class="alert err"><i class="fa fa-circle-exclamation"></i><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <div class="roles-label">Your role</div>
      <div class="roles">
        <?php
        $roles = [
          ['r'=>'Patient',      'icon'=>'fa-hospital-user','lbl'=>'Patient'],
          ['r'=>'Doctor',       'icon'=>'fa-user-doctor',  'lbl'=>'Doctor'],
          ['r'=>'User',         'icon'=>'fa-user-tie',     'lbl'=>'Employee'],
          ['r'=>'Admin',        'icon'=>'fa-shield-halved','lbl'=>'Admin'],
          ['r'=>'System Admin', 'icon'=>'fa-crown',        'lbl'=>'Super Admin','cls'=>'super'],
        ];
        foreach ($roles as $ro):
          $active = ($sel === $ro['r']) ? 'active' : '';
          $cls    = $active . ' ' . ($ro['cls'] ?? '');
        ?>
        <button type="button" class="role-btn <?= $cls ?>"
                data-role="<?= $ro['r'] ?>">
          <i class="fa <?= $ro['icon'] ?>"></i><?= $ro['lbl'] ?>
        </button>
        <?php endforeach; ?>
      </div>

      <form method="POST" id="login-form">
        <input type="hidden" name="action" value="login">
        <input type="hidden" name="role" id="role-input" value="<?= htmlspecialchars($sel) ?>">

        <div class="field">
          <label id="id-label">National ID</label>
          <i class="fa fa-id-card ico" id="id-icon"></i>
          <input type="text" name="identifier" id="identifier"
                 placeholder="Enter your National ID"
                 value="<?= htmlspecialchars($_POST['identifier'] ?? '') ?>"
                 autocomplete="username" required>
        </div>

        <div class="field">
          <label>Password</label>
          <i class="fa fa-lock ico"></i>
          <input type="password" name="password" placeholder="Enter your password"
                 autocomplete="current-password" required>
        </div>

        <button type="submit" class="btn-submit">
          <i class="fa fa-right-to-bracket"></i> Sign In
        </button>
      </form>

      <div class="divider"><span>New patient?</span></div>
      <button class="btn-submit" onclick="togglePanel()"
              style="background:transparent;color:var(--blue);box-shadow:none;border:1.5px solid var(--blue);margin-top:0;">
        <i class="fa fa-user-plus"></i> Create Account
      </button>
    </div>
  </div>

  <!-- ── SIGN UP FORM ── -->
  <div class="form-box signup">
    <div class="form-inner">
      <h1>Create Account</h1>
      <p class="sub">Register as a new patient</p>

      <?php if ($signup_error): ?>
      <div class="alert err"><i class="fa fa-circle-exclamation"></i><?= htmlspecialchars($signup_error) ?></div>
      <?php endif; ?>
      <?php if ($signup_ok): ?>
      <div class="alert ok"><i class="fa fa-circle-check"></i><?= htmlspecialchars($signup_ok) ?></div>
      <?php endif; ?>

      <form method="POST" id="reg-form">
        <input type="hidden" name="action" value="register">

        <div class="field">
          <label>Full Name</label>
          <i class="fa fa-user ico"></i>
          <input type="text" name="fullName" placeholder="Patient Name" required
                 value="<?= htmlspecialchars($_POST['fullName'] ?? '') ?>">
        </div>

        <div class="field-row">
          <div class="field">
            <label>Age</label>
            <i class="fa fa-hourglass-half ico"></i>
            <input type="number" name="age" id="age" placeholder="Age" min="1" required
                  value="<?= htmlspecialchars($_POST['age'] ?? '') ?>">
          </div>

          <div class="field">
            <label>Gender</label>
            <i class="fa fa-venus-mars ico"></i>
            <select name="gender" id="gender" required>
              <option value="">Select Gender</option>
              <option value="Male" <?= (($_POST['gender'] ?? '') === 'Male') ? 'selected' : '' ?>>Male</option>
              <option value="Female" <?= (($_POST['gender'] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
            </select>
          </div>
        </div>

        <div class="field">
          <label>National ID</label>
          <i class="fa fa-id-card ico"></i>
          <input type="text" name="nat_id" placeholder="National ID" required
                 value="<?= htmlspecialchars($_POST['nat_id'] ?? '') ?>">
        </div>

        <div class="field">
          <label>Email</label>
          <i class="fa fa-envelope ico"></i>
          <input type="email" name="reg_email" placeholder="your@email.com" required
                 value="<?= htmlspecialchars($_POST['reg_email'] ?? '') ?>">
        </div>

        <div class="field-row">
          <div class="field">
            <label>Password</label>
            <i class="fa fa-lock ico"></i>
            <input type="password" name="reg_pass" id="pass1" placeholder="Password" required>
          </div>
          <div class="field">
            <label>Confirm</label>
            <i class="fa fa-lock ico"></i>
            <input type="password" name="reg_pass2" id="pass2" placeholder="Confirm" required>
          </div>
        </div>

        <button type="submit" class="btn-submit">
          <i class="fa fa-user-plus"></i> Sign Up
        </button>
      </form>

      <div class="divider"><span>Already have an account?</span></div>
      <button class="btn-submit" onclick="togglePanel()"
              style="background:transparent;color:var(--blue);box-shadow:none;border:1.5px solid var(--blue);margin-top:0;">
        <i class="fa fa-right-to-bracket"></i> Sign In
      </button>
    </div>
  </div>

</div><!-- /wrapper -->

<script>
const wrapper  = document.getElementById('wrapper');
const roleBtns = document.querySelectorAll('.role-btn');
const roleInp  = document.getElementById('role-input');
const idLabel  = document.getElementById('id-label');
const idIcon   = document.getElementById('id-icon');
const identInp = document.getElementById('identifier');

const panelStates = {
  login: {
    title: 'Welcome<br>Back!',
    desc:  'Sign in with your credentials<br>to access your dashboard.',
    cta:   'Register',
    icon:  'fa-user-plus',
  },
  register: {
    title: 'Hello,<br>Friend!',
    desc:  'Register with your personal details<br>to use all site features.',
    cta:   'Sign In',
    icon:  'fa-right-to-bracket',
  }
};

function updatePanel(state) {
  const s = panelStates[state];
  document.getElementById('panel-title').innerHTML      = s.title;
  document.getElementById('panel-desc').innerHTML       = s.desc;
  document.getElementById('panel-cta-text').textContent = s.cta;
  const ico = document.getElementById('panel-icon');
  ico.className = 'fa ' + s.icon;
}

function togglePanel() {
  const isActive = wrapper.classList.toggle('active');
  updatePanel(isActive ? 'register' : 'login');
}

function activateRole(btn) {
  roleBtns.forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  const role = btn.getAttribute('data-role');
  roleInp.value = role;
  identInp.value = '';

  if (role === 'Patient') {
    idLabel.textContent = 'National ID';
    idIcon.className = 'fa fa-id-card ico';
    identInp.placeholder = 'Enter your National ID';
    identInp.type = 'text';
  } else {
    idLabel.textContent = 'Email';
    idIcon.className = 'fa fa-envelope ico';
    identInp.placeholder = 'Enter your email';
    identInp.type = 'text';
  }
  identInp.focus();
}

roleBtns.forEach(b => b.addEventListener('click', () => activateRole(b)));

const pre = roleInp.value;
if (pre) {
  const m = [...roleBtns].find(b => b.getAttribute('data-role') === pre);
  if (m) activateRole(m);
}

if (wrapper.classList.contains('active')) updatePanel('register');

const pass1 = document.getElementById('pass1');
const pass2 = document.getElementById('pass2');
if (pass2) {
  pass2.addEventListener('input', () => {
    pass2.style.borderColor = pass1.value === pass2.value ? '#27ae60' : '#e74c3c';
  });
}
</script>

<script src="/assets/js/responsive-nav.js" defer></script>
</body>
</html>
