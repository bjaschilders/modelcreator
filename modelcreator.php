<?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $title = trim($_POST["title"]);
        $capitalTitle = ucfirst($title);
        $tableTitle = trim($_POST["tabletitle"]);
        $tableId = trim($_POST["tableid"]);
        $title = strtolower(str_replace(' ', '', $title));
        $relationship = trim($_POST["relationship"]);
        $relationships = lcfirst($relationship) . "s";
        $relationshipcapital = ucfirst($relationship);
        $relationshipDefinition = trim($_POST["relationshipdefine"]);
        
        $fileName = ucfirst($title) . '.php';
        $filePath = __DIR__ . '/' . $fileName;

        $columns = '';
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'column_title_') === 0) {
                $index = str_replace('column_title_', '', $key);
                $columnTitle = $value;
                $columns .= "'$columnTitle', ";
            }
        }

        $fileContent = <<<EOT
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class $capitalTitle extends Model
{
    use HasFactory;

    protected \$table = '$tableTitle';

    protected \$primaryKey = '$tableId';

    public \$incrementing = true;
    protected \$keyType = 'int';

    protected \$fillable = [$columns];

    public function $relationships()
    {
        return \$this->$relationshipDefinition($relationshipcapital::class);
    }
}

EOT;

        if (file_put_contents($filePath, $fileContent) !== false) {
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1&filename=" . urlencode($fileName));
            exit();
        } else {
            echo "<p>Error: Unable to create the file.</p>";
        }
    }

    if (isset($_GET['success']) && $_GET['success'] == 1 && isset($_GET['filename'])) {
        $fileName = htmlspecialchars($_GET['filename']);
        echo "<p>File <strong>$fileName</strong> created successfully.</p>";
    }
    ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Model Generator</title>
    <style>
        .dynamic-field { margin-bottom: 15px; }
    </style>
    <script>
        let fieldCount = 0;

        function addField() {
            fieldCount++;
            const container = document.getElementById('dynamic-fields');

            const fieldDiv = document.createElement('div');
            fieldDiv.classList.add('dynamic-field');
            fieldDiv.id = `field-${fieldCount}`;

            fieldDiv.innerHTML = `
                <label for="title-${fieldCount}">Fillables:</label>
                <input type="text" name="column_title_${fieldCount}" id="title-${fieldCount}" required>

            `;

            container.appendChild(fieldDiv);
        }

        function handleTypeChange(index) {
            const typeSelect = document.getElementById(`type-${index}`);
            const foreignFields = document.getElementById(`foreign-fields-${index}`);
            const randomNumbFields = document.getElementById(`random-number-${index}`);
            const betweenNumbFields = document.getElementById(`between-number-${index}`);
            const randomTextFields = document.getElementById(`random-text-${index}`);
            const specificNumbFields = document.getElementById(`specific-number-${index}`);

            if (typeSelect.value === 'randnumb') {
                randomNumbFields.style.display = 'block';
            } else {
                randomNumbFields.style.display = 'none';
            }

            if (typeSelect.value === 'randtext') {
                randomTextFields.style.display = 'block';
            } else {
                randomTextFields.style.display = 'none';
            }

            if (typeSelect.value === 'randbetweennumb') {
                betweenNumbFields.style.display = 'block';
            } else {
                betweenNumbFields.style.display = 'none';
            }

            if (typeSelect.value === 'specnumb') {
                specificNumbFields.style.display = 'block';
            } else {
                specificNumbFields.style.display = 'none';
            }
        }
    </script>
</head>
<body>
    <h1>Generate PHP Model File</h1>
    <form action="" method="POST">
        <label for="title">Model Title:</label>
        <input type="text" id="title" name="title" required>
        <br><br>
        <label for="tabletitle">Table Name:</label>
        <input type="text" id="tabletitle" name="tabletitle" required>
        <br><br>
        <label for="tableid">Table Primary Key (if left blank will be simply Id):</label>
        <input type="text" id="tableid" name="tableid">
        <br><br>
        <label for="relationship">Relationship With Another):</label>
        <input type="text" id="relationship" name="relationship">
        <select name="relationshipdefine">
            <option value="hasOne">Has One</option>
            <option value="hasMany">Has Many</option>
            <option value="belongsToOne">Belongs To One</option>
            <option value="belongsToMany">Belongs To Many</option>
        </select>
        <br><br>

        <h3>Define Fillables</h3>
        <div id="dynamic-fields"></div>
        <button type="button" onclick="addField()">+ Add Column</button>
        <br><br>
        <button type="submit">Generate File</button>
    </form>

    
</body>
</html>
