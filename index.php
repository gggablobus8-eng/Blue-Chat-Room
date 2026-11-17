<?php
// =========================================================================
// HYBRID PHP BACKEND (Executes only when hosted on a PHP-enabled server)
// Ignored automatically by GitHub Pages static hosting.
// =========================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_post') {
    header('Content-Type: application/json');
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $allowedExts = ['jpg','jpeg','png','gif','webp','bmp','mp4','webm','ogg','avi','mov','rar','zip','exe','mp3','mp2','mp1','mp5','wav','flac'];
    $user = htmlspecialchars(trim($_POST['user'] ?? 'Guest7'), ENT_QUOTES, 'UTF-8');
    $text = htmlspecialchars(trim($_POST['text'] ?? ''), ENT_QUOTES, 'UTF-8');
    $time = date('h:i A');
    $fileMeta = null;

    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $origName = basename($_FILES['file']['name']);
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExts)) {
            echo json_encode(['status' => 'error', 'message' => 'File type not allowed.']);
            exit;
        }

        // Sanitize filename and save to uploads/
        $safeName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $origName);
        $targetPath = $uploadDir . $safeName;
        if (file_exists($targetPath)) {
            $safeName = time() . '_' . $safeName;
            $targetPath = $uploadDir . $safeName;
        }

        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
            $fileMeta = [
                'name' => $origName,
                'ext' => $ext,
                'size' => $_FILES['file']['size'],
                'url' => 'uploads/' . $safeName,
                'uploadPath' => 'uploads/' . $safeName
            ];
        }
    }

    if ($text !== '' || $fileMeta !== null) {
        $postObj = [
            'id' => round(microtime(true) * 1000),
            'user' => $user,
            'time' => $time,
            'text' => $text,
            'file' => $fileMeta
        ];
        file_put_contents(__DIR__ . '/posts.txt', json_encode($postObj) . PHP_EOL, FILE_APPEND | LOCK_EX);
        echo json_encode(['status' => 'ok', 'post' => $postObj]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Empty post.']);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Blue Chat Rooms Lite - Windows 7 Edition</title>
<script src="https://cloudflare.com"></script>
<style>
  :root {
    --aero-glass: rgba(185, 218, 245, 0.55);
    --aero-border: rgba(255, 255, 255, 0.75);
    --aero-dark-border: rgba(24, 56, 88, 0.65);
  }

  * {
    box-sizing: border-box;
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    user-select: none;
  }

  body {
    margin: 0;
    height: 100vh;
    overflow: hidden;
    background: radial-gradient(circle at 20% 25%, #5cc4ff 0%, #1c7ed6 40%, #0b3975 85%, #061e42 100%);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  body::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(125deg, rgba(255,255,255,0.22) 0%, rgba(255,255,255,0) 45%, rgba(116, 208, 255, 0.25) 70%, transparent 100%);
    pointer-events: none;
  }

  /* ================= AERO WINDOW STYLING ================= */
  .aero-window {
    position: absolute;
    background: var(--aero-glass);
    backdrop-filter: blur(14px) saturate(140%);
    -webkit-backdrop-filter: blur(14px) saturate(140%);
    border: 1px solid var(--aero-dark-border);
    border-radius: 8px;
    box-shadow: inset 0 0 0 1px var(--aero-border), 0 12px 32px rgba(0, 0, 0, 0.55);
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .title-bar {
    height: 30px;
    padding: 0 8px 0 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: move;
    background: linear-gradient(to bottom, rgba(255,255,255,0.65) 0%, rgba(255,255,255,0.15) 50%, rgba(160,205,245,0.25) 51%, rgba(255,255,255,0.35) 100%);
    text-shadow: 0 0 6px #fff, 0 0 10px #fff;
    font-size: 13px;
    font-weight: 600;
    color: #0b2239;
  }

  .title-bar-controls {
    display: flex;
    gap: 2px;
    margin-top: -4px;
  }

  .win-btn {
    width: 28px;
    height: 20px;
    border: 1px solid rgba(0,0,0,0.45);
    border-top: none;
    border-radius: 0 0 4px 4px;
    background: linear-gradient(to bottom, rgba(255,255,255,0.7), rgba(180,210,235,0.4) 50%, rgba(120,165,205,0.5) 51%, rgba(190,225,255,0.7));
    color: #111;
    font-size: 11px;
    font-weight: bold;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: inset 0 0 2px #fff;
  }

  .win-btn:hover {
    background: linear-gradient(to bottom, #fff, #bce0fd 50%, #70b9f7 51%, #c2e4ff);
  }

  .win-btn.close {
    width: 42px;
    background: linear-gradient(to bottom, #f59c95, #e04336 50%, #c42316 51%, #e86b61);
    color: white;
    text-shadow: 0 1px 2px #000;
  }

  .win-btn.close:hover {
    background: linear-gradient(to bottom, #ffb5af, #ff5447 50%, #e02214 51%, #ff8278);
  }

  .window-body {
    margin: 0 6px 6px 6px;
    background: #ffffff;
    border: 1px solid #6b8ba8;
    border-radius: 3px;
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  /* ================= MAIN CHAT WINDOW ================= */
  #main-chat-win {
    width: 840px;
    height: 570px;
    top: 25px;
    left: calc(50% - 420px);
    z-index: 10;
  }

  .chat-header-banner {
    background: linear-gradient(to bottom, #eef6fc 0%, #d4e8f9 100%);
    border-bottom: 1px solid #a9c6df;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .user-badge {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .aero-avatar {
    width: 42px;
    height: 42px;
    border-radius: 5px;
    border: 2px solid #72b4e8;
    box-shadow: 0 0 6px rgba(45, 145, 235, 0.6);
    background: linear-gradient(135deg, #6ec6ff, #005bb5);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 18px;
  }

  .mode-pill {
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 10px;
    background: #d8ecff;
    border: 1px solid #7ab5eb;
    color: #0e3d6b;
    font-weight: 600;
  }

  .chat-area {
    flex: 1;
    padding: 12px;
    overflow-y: auto;
    background: #f9fcff;
    user-select: text;
  }

  .post-item {
    background: #ffffff;
    border: 1px solid #d4e3f0;
    border-radius: 5px;
    padding: 8px 12px;
    margin-bottom: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
  }

  .post-meta {
    font-size: 12px;
    color: #2b5d8c;
    font-weight: bold;
    margin-bottom: 4px;
    display: flex;
    justify-content: space-between;
  }

  .post-meta span.time {
    color: #7b93a8;
    font-weight: normal;
  }

  .post-text {
    font-size: 13.5px;
    color: #1c2833;
    word-wrap: break-word;
  }

  .post-text a {
    color: #0066cc;
    text-decoration: underline;
    cursor: pointer;
  }

  .attachment-card {
    margin-top: 8px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 10px;
    background: linear-gradient(to bottom, #f5faff, #e1effb);
    border: 1px solid #9ec3e6;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12.5px;
    color: #0f3b66;
  }

  .attachment-card:hover {
    background: linear-gradient(to bottom, #e6f3ff, #cce5fc);
    border-color: #5ca2e6;
  }

  .chat-controls {
    background: #e6f0fa;
    border-top: 1px solid #b5cde3;
    padding: 10px;
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .toolbar {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .win7-btn {
    padding: 4px 12px;
    font-size: 12px;
    color: #111;
    border: 1px solid #707070;
    border-radius: 3px;
    background: linear-gradient(to bottom, #f2f2f2 0%, #ebebeb 50%, #dddddd 51%, #cfcfcf 100%);
    cursor: pointer;
    box-shadow: inset 0 0 1px #fff;
  }

  .win7-btn:hover {
    border-color: #3c7fb1;
    background: linear-gradient(to bottom, #eaf6fd 0%, #d9f0fc 50%, #bee6fd 51%, #a7d9f5 100%);
  }

  .win7-input {
    padding: 5px 8px;
    border: 1px solid #8ea8c2;
    border-radius: 3px;
    font-size: 13px;
    outline: none;
  }

  .win7-input:focus {
    border-color: #3c7fb1;
    box-shadow: 0 0 4px rgba(60, 127, 177, 0.5);
  }

  .input-row {
    display: flex;
    gap: 8px;
  }

  #msg-input {
    flex: 1;
  }

  /* ================= WINDOWS MEDIA PLAYER 11 WINDOW ================= */
  #wmp-win {
    width: 540px;
    height: 410px;
    top: 80px;
    left: 120px;
    display: none;
    z-index: 30;
  }

  .wmp-body {
    background: #0a0f18;
    color: #fff;
    display: flex;
    flex-direction: column;
    height: 100%;
  }

  .wmp-top-nav {
    height: 28px;
    background: linear-gradient(to bottom, #3a4859, #1c2530 50%, #0e141c 51%, #1b2430);
    display: flex;
    align-items: center;
    padding: 0 10px;
    gap: 15px;
    font-size: 12px;
    border-bottom: 1px solid #000;
  }

  .wmp-tab {
    padding: 3px 10px;
    border-radius: 10px;
    background: linear-gradient(to bottom, #4ea3f5, #1862b5);
    font-weight: bold;
    box-shadow: 0 0 5px rgba(78, 163, 245, 0.7);
  }

  .wmp-stage {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: radial-gradient(circle, #182c47 0%, #05080d 100%);
    overflow: hidden;
    position: relative;
  }

  .wmp-stage video, .wmp-stage img {
    max-width: 100%;
    max-height: 100%;
  }

