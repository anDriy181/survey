<?php
// =======================================================================
// PHP-БЛОК: ОБРОБКА, ЗБЕРЕЖЕННЯ ДАНИХ АНКЕТИ ТА ЛОГІКА ВІДОБРАЖЕННЯ
// =======================================================================

$form_submitted = isset($_POST['submit_quiz']);
$output = ''; // Змінна для зберігання виведених результатів

if ($form_submitted) {
    
    // 1. Збір та очищення (санітизація) даних
    $name = htmlspecialchars($_POST['respondent_name'] ?? 'Не вказано');
    $email = htmlspecialchars($_POST['respondent_email'] ?? 'Не вказано');
    $q1_os = htmlspecialchars($_POST['q1_os'] ?? 'Не обрано');
    $q2_experience = htmlspecialchars($_POST['q2_experience'] ?? 'Не вказано');
    
    // Обробка чекбоксів
    $q3_languages_array = $_POST['q3_languages'] ?? []; 
    $q3_languages_safe = array_map('htmlspecialchars', $q3_languages_array);
    $q3_languages_string = empty($q3_languages_safe) ? 'Не обрано' : implode(', ', $q3_languages_safe);
    
    
    // 2. Логіка зберігання файлів
    $data_dir = 'results/'; // Назва папки для зберігання
    $file_name = $data_dir . 'survey_data.txt'; 
    $save_message = '';

    // 2.1. Створення папки, якщо вона не існує
    if (!is_dir($data_dir)) {
        if (!mkdir($data_dir, 0777, true)) {
            $save_message = "Помилка: Не вдалося створити папку <code>$data_dir</code>. Перевірте права доступу.";
        }
    }
    
    // 2.2. Запис даних, якщо папка існує або була успішно створена
    if (is_dir($data_dir)) {
        $timestamp = date('Y-m-d H:i:s');
        $data_to_save = 
            "-----------------------------------------\n" .
            "Дата та час: $timestamp\n" .
            "Ім'я: $name\n" .
            "Email: $email\n" .
            "Q1 (ОС): $q1_os\n" .
            "Q2 (Досвід): $q2_experience\n" .
            "Q3 (Мови): $q3_languages_string\n" .
            "-----------------------------------------\n\n";
            
        // Запис даних у файл
        if (file_put_contents($file_name, $data_to_save, FILE_APPEND | LOCK_EX) !== false) {
            $save_message = "Дані успішно збережено у файлі <code>$file_name</code>.";
        } else {
            $save_message = "Помилка при збереженні даних. Перевірте, чи дозволено запис у файл.";
        }
    }

    // 3. Формування результату для виведення на екран
    $output .= "<h2>✅ Результати опитування успішно отримано!</h2>";
    $output .= "<p style='color: " . (strpos($save_message, 'Помилка') === false ? 'green' : 'red') . "; font-weight: bold;'>$save_message</p>";
    $output .= "<div style='border: 1px solid #ccc; padding: 15px; background-color: #f9f9f9;'>";
    $output .= "<h3>👤 Дані респондента:</h3>";
    $output .= "<p><strong>Ім'я:</strong> $name</p>";
    $output .= "<p><strong>Email:</strong> $email</p>";
    $output .= "<hr>";
    $output .= "<h3>📋 Відповіді на питання:</h3>";
    $output .= "<p><strong>1. Улюблена операційна система:</strong> $q1_os</p>";
    $output .= "<p><strong>2. Як ви оцінюєте свій досвід у програмуванні (0-10):</strong> $q2_experience</p>";
    $output .= "<p><strong>3. З якими мовами програмування ви знайомі:</strong> $q3_languages_string</p>";
    $output .= "</div>";
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Анкета Опитування (POST)</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; }
        form { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        fieldset { margin-bottom: 20px; padding: 15px; border: 1px solid #eee; border-radius: 5px; }
        legend { font-weight: bold; padding: 0 10px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="number"], textarea { width: 98%; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .radio-group label, .checkbox-group label { display: inline; font-weight: normal; margin-right: 15px; }
        input[type="submit"] { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        input[type="submit"]:hover { background-color: #0056b3; }
        .result-container { max-width: 600px; margin: 0 auto; padding: 20px; }
    </style>
</head>
<body>

    <div class="result-container">
        <?php echo $output; // Виводимо результати, якщо форма була відправлена ?>

        <?php if (!$form_submitted): // Виводимо форму, якщо вона ще НЕ була відправлена ?>
        
            <h1>📝 Анкета Опитування (Тема: IT та Розробка)</h1>

            <form action="" method="POST"> 
                
                <fieldset>
                    <legend>Дані респондента</legend>
                    <label for="name">Ім'я респондента:</label>
                    <input type="text" id="name" name="respondent_name" required>
                    
                    <label for="email">Email респондента:</label>
                    <input type="email" id="email" name="respondent_email" required>
                </fieldset>

                <fieldset>
                    <legend>Питання опитування</legend>
                    
                    <p>1. Яка ваша улюблена операційна система для роботи?</p>
                    <div class="radio-group">
                        <input type="radio" id="q1_win" name="q1_os" value="Windows" required>
                        <label for="q1_win">Windows</label>
                        
                        <input type="radio" id="q1_mac" name="q1_os" value="macOS">
                        <label for="q1_mac">macOS</label>
                        
                        <input type="radio" id="q1_linux" name="q1_os" value="Linux">
                        <label for="q1_linux">Linux</label>
                    </div>

                    <br>
                    
                    <label for="q2_experience">2. Як ви оцінюєте свій досвід у програмуванні? (від 0 до 10)</label>
                    <input type="number" id="q2_experience" name="q2_experience" min="0" max="10" required>
                    
                    <br>

                    <p>3. З якими мовами програмування ви знайомі? (Оберіть усі, що підходять)</p>
                    <div class="checkbox-group">
                        <input type="checkbox" id="q3_php" name="q3_languages[]" value="PHP">
                        <label for="q3_php">PHP</label><br>
                        
                        <input type="checkbox" id="q3_js" name="q3_languages[]" value="JavaScript">
                        <label for="q3_js">JavaScript</label><br>
                        
                        <input type="checkbox" id="q3_py" name="q3_languages[]" value="Python">
                        <label for="q3_py">Python</label><br>
                        
                        <input type="checkbox" id="q3_other" name="q3_languages[]" value="Інша">
                        <label for="q3_other">Інша</label>
                    </div>
                </fieldset>
                
                <input type="submit" name="submit_quiz" value="Надіслати анкету">
            </form>
            
        <?php endif; ?>
    </div>

</body>
</html>