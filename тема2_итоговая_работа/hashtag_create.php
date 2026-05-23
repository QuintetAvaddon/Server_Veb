<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$db = getDB();
$name = isset($_GET['new']) && isset($_SESSION['pending_hashtag']) 
    ? $_SESSION['pending_hashtag'] 
    : '';

$fields = $db->query("SELECT * FROM Fields ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = strtolower(trim($_POST['name']));
    $data = trim($_POST['data'] ?? '');
    $selectedFields = $_POST['fields'] ?? [];
    
    if (empty($name)) {
        $error = 'Введите название хэштега';
    } else {
        $stmt = $db->prepare("SELECT id FROM Hashtags WHERE name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetch() && !isset($_SESSION['pending_hashtag'])) {
            $error = 'Такой хэштег уже существует';
        } else {
            $stmt = $db->prepare("INSERT INTO Hashtags (name, data) VALUES (?, ?) ON DUPLICATE KEY UPDATE data = VALUES(data)");
            $stmt->execute([$name, $data]);
            $hashtagId = $db->lastInsertId() ?: $db->query("SELECT id FROM Hashtags WHERE name = '$name'")->fetch()['id'];
            
            $stmt = $db->prepare("DELETE FROM Hashtag_Fields WHERE id_hashtag = ?");
            $stmt->execute([$hashtagId]);
            
            foreach ($selectedFields as $fieldId) {
                $stmt = $db->prepare("INSERT INTO Hashtag_Fields (id_hashtag, id_field) VALUES (?, ?)");
                $stmt->execute([$hashtagId, $fieldId]);
            }
            
            if (isset($_SESSION['pending_sms'])) {
                $pending = $_SESSION['pending_sms'];
                $stmt = $db->prepare("INSERT INTO SMS (hashtag_id, user_id, channel_id, description, is_saved) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $hashtagId,
                    $_SESSION['user_id'],
                    $pending['channel_id'],
                    $pending['description'],
                    $pending['is_saved']
                ]);
                unset($_SESSION['pending_sms'], $_SESSION['pending_hashtag']);
                header('Location: index.php?success=1');
                exit;
            }
            
            header('Location: hashtags.php?created=1');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Создание хэштега</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f6fa; color: #2c3e50; }
        .container { max-width: 600px; margin: 40px auto; padding: 0 20px; }
        .header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 25px; border-radius: 12px; margin-bottom: 30px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; }
        input, textarea, select { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; }
        input:focus, textarea:focus, select:focus { outline: none; border-color: #667eea; }
        .fields-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-top: 10px; }
        .field-item { display: flex; align-items: center; gap: 8px; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; cursor: pointer; }
        .field-item:hover { border-color: #667eea; }
        .field-item input { width: auto; }
        .btn { background: linear-gradient(135deg, #667eea, #764ba2); color: white; border: none; padding: 14px 30px; border-radius: 8px; font-size: 1rem; cursor: pointer; }
        .alert-error { background: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
        .hint { color: #888; font-size: 0.9rem; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?php echo isset($_SESSION['pending_hashtag']) ? 'Привязка хэштега' : 'Новый хэштег'; ?></h1>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <form method="post">
                <div class="form-group">
                    <label for="name">Название хэштега</label>
                    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>" <?php echo isset($_SESSION['pending_hashtag']) ? 'readonly' : ''; ?> placeholder="cake">
                    <p class="hint">Без символа #, только буквы и цифры</p>
                </div>
                
                <div class="form-group">
                    <label for="data">Дополнительная информация</label>
                    <textarea name="data" id="data" rows="3" placeholder="Описание хэштега"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Области знаний</label>
                    <div class="fields-grid">
                        <?php foreach ($fields as $field): ?>
                            <label class="field-item">
                                <input type="checkbox" name="fields[]" value="<?php echo $field['id']; ?>">
                                <?php echo htmlspecialchars($field['name']); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <p class="hint">Выберите одну или несколько областей знаний для этого хэштега</p>
                </div>
                
                <button type="submit" class="btn">Сохранить</button>
            </form>
        </div>
    </div>
</body>
</html>