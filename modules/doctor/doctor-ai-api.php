<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// ── Auth guard (Doctors Only) ────────────────────────────────────────────────
if (empty($_SESSION['login']) || ($_SESSION['role'] ?? '') !== 'Doctor') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized or Not a Doctor'], JSON_UNESCAPED_UNICODE);
    exit();
}

$inputRaw = file_get_contents('php://input');
$input = json_decode($inputRaw, true);
$action = $input['action'] ?? '';

if (!$input || !$action) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request'], JSON_UNESCAPED_UNICODE);
    exit();
}

$apiKey = 'YOUR_OPENAI_API_KEY';
$url = 'https://api.openai.com/v1/chat/completions';

// ── Action: generate_visit_note ──────────────────────────────────────────────
if ($action === 'generate_visit_note') {
    $notes = $input['notes'] ?? '';
    if (!$notes) {
        echo json_encode(['error' => 'No notes provided'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $systemPrompt = "You are an expert AI Clinical DOCUMENTATION ASSISTANT. 
    Your goal is to take raw, messy medical notes (Complaint, Vitals, Exam, Plan) and transform them into professional, structured clinical documentation.
    
    Output MUST be in JSON format with the following keys:
    1. 'visit_summary': A concise summary of the visit (Arabic).
    2. 'clinical_note': A structured SOAP note (Arabic).
    3. 'discharge_summary': A summary for discharge (Arabic).
    4. 'follow_up': Follow-up instructions for the patient (Arabic).
    5. 'suggested_prescription': Recommended medications based on the plan (Arabic).
    6. 'suggested_scans': Recommended scans or tests (Arabic).

    Rules:
    - Language: Arabic (Modern Standard Clinical).
    - Tone: Professional, medical.
    - Be precise. If a medication is mentioned, include it.";

    $messages = [
        ['role' => 'system', 'content' => $systemPrompt],
        ['role' => 'user', 'content' => "Raw Notes:\n" . $notes]
    ];

    $payload = [
        'model' => 'gpt-4o-mini',
        'messages' => $messages,
        'response_format' => ['type' => 'json_object'],
        'temperature' => 0.4
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ],
        CURLOPT_TIMEOUT => 30,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        echo json_encode(['error' => 'AI Service Error'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $aiData = json_decode($response, true);
    $resultRaw = $aiData['choices'][0]['message']['content'];
    echo $resultRaw; // Return the JSON from AI
    exit();
}

// ── Action: summarize_history ────────────────────────────────────────────────
if ($action === 'summarize_history') {
    $patientId = (int)($input['patient_id'] ?? 0);
    if (!$patientId) {
        echo json_encode(['error' => 'Invalid Patient ID'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $connect = new mysqli('localhost', 'root', '', 'hms');
    if ($connect->connect_error) {
        echo json_encode(['error' => 'DB Connection Error'], JSON_UNESCAPED_UNICODE);
        exit();
    }
    $connect->set_charset('utf8mb4');

    // Fetch all medical records
    $history = [];
    $stmt = $connect->prepare("SELECT prescription, Scan, description, CreationDate FROM tblmedicalhistory WHERE userId = ? ORDER BY CreationDate DESC");
    $stmt->bind_param("i", $patientId);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $history[] = "Date: {$row['CreationDate']} | Treatment: {$row['prescription']} | Scans: {$row['Scan']} | Note: {$row['description']}";
    }
    $stmt->close();
    $connect->close();

    if (empty($history)) {
        echo json_encode(['summary' => 'لا يوجد سجل طبي سابق لهذا المريض.'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    $historyText = implode("\n---\n", $history);

    $systemPrompt = "You are an expert AI Medical Reviewer. 
    Summarize the following patient medical history into a concise clinical timeline and overview.
    Focus on recurring issues, chronic diagnoses, and recent treatments.
    Language: Arabic. Use professional tone. Use bullet points.";

    $messages = [
        ['role' => 'system', 'content' => $systemPrompt],
        ['role' => 'user', 'content' => "Patient History:\n" . $historyText]
    ];

    $payload = [
        'model' => 'gpt-4o-mini',
        'messages' => $messages,
        'temperature' => 0.3
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ],
        CURLOPT_TIMEOUT => 30,
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $aiData = json_decode($response, true);
    $summary = $aiData['choices'][0]['message']['content'];

    echo json_encode(['summary' => $summary], JSON_UNESCAPED_UNICODE);
    exit();
}
