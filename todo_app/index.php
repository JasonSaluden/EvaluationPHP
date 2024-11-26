<?php
// Session démarrée
session_start();

// Si user non connecté, go login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Si aucune tâche, initialisation d'un tableau vide
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

// Ajouter une nouvelle tâche
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nouvelleTask'])) {
    // Definitions des différentes variables
    $newTask = htmlspecialchars(trim($_POST['nouvelleTask']));
    // Si description il y a
    $description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : '';
    // Format de la date  à appliquer
    $currentDate = date('d/m/Y');
    // Si le champ 'nouvelleTask' n'est pas vide
    if (!empty($newTask)) {
        // Tableau associatif de dates
        $_SESSION['tasks'][] = [
            "identifiant" => uniqid(),
            "text" => $newTask,
            "description" => $description,
            "completed" => false,
            "date_added" => $currentDate
        ];
    }
}

// Marquer une tâche comme complétée, grace à l'ID
if (isset($_POST['complete-task'])) {
    $taskId = $_POST['complete-task'];
    foreach ($_SESSION['tasks'] as $index => $task) {
        if ($task['identifiant'] === $taskId) {
            $_SESSION['tasks'][$index]['completed'] = true;
            break;
        }
    }
}

// Suppression tach indiv
if(isset($_POST['delete-task'])) {
    $taskId = $_POST['delete-task'];
    foreach ($_SESSION['tasks'] as $index => $task) {
        if ($task['identifiant'] === $taskId) {
            unset($_SESSION['tasks'][$index]);
            $_SESSION['tasks'] = array_values($_SESSION['tasks']);
            break;
        }
    }
}

// Supprimer les tâches complétées
if (isset($_POST['clear-completed'])) {
    $_SESSION['tasks'] = array_filter($_SESSION['tasks'], fn($task) => !$task['completed']);
}



// Redirection déconnexion
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-do List</title>
    <link rel="stylesheet" href="index.css" />

</head>

<body>
    <header>
        <a href="?logout=1">
            <button href="?logout=1" class="logout">
                <svg b-9bnxh5y84m="" xmlns="http://www.w3.org/2000/svg" width="22.893" height="25.106" viewBox="0 0 22.893 25.106">
                    <g b-9bnxh5y84m="" id="Icon_feather-power" data-name="Icon feather-power" transform="translate(1.5 1.5)">
                        <path b-9bnxh5y84m="" id="Tracé_1146" data-name="Tracé 1146" d="M21.473,9.96a9.03,9.03,0,0,1,0,13.284,10.365,10.365,0,0,1-14.065,0A9.03,9.03,0,0,1,7.4,9.96" transform="translate(-4.493 -3.889)" fill="none" stroke="#052940" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"></path>
                        <path b-9bnxh5y84m="" id="Tracé_1147" data-name="Tracé 1147" d="M18,3V16.083" transform="translate(-8.048 -3)" fill="none" stroke="#052940" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"></path>
                    </g>
                </svg>
            </button>
        </a>
    </header>
    <div class="globalHeight">
        <div class="container">
            <h1 class="titre">Bienvenue, <?= htmlspecialchars($_SESSION['username']); ?> !</h1>
            <h3 class="sous-titre">Votre liste de choses à faire :</h3>
            <!-- Ajout des tâches -->
            <form method="POST" class="form">
                <input class="input" type="text" name="nouvelleTask" placeholder="Nouvelle tâche" required>
                <input class="input" type="text" name="description" placeholder="Description">
                <button type="submit" class="addBtn">Ajouter tâche</button>
            </form>
            <!-- Si la liste est vide... -->
            <?php if (empty($_SESSION['tasks'])): ?>
                <p>La liste est vide. Ajoutez une tâche pour commencer.</p>
            <!-- Sinon -->
            <?php else: ?>
                <table>
                    <thead>
                        <tr class="headRow">
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Pour chaque tâche ajoutée -->
                        <?php foreach ($_SESSION['tasks'] as $task): ?>
                            <!-- Ajout de la classe .completed ou non en fction de l'état -->
                            <tr class="<?php echo $task['completed'] ? 'completed' : ''; ?> contenuTableau">
                                <td><?php echo htmlspecialchars($task['identifiant']); ?></td>
                                <td>
                                    <strong>
                                        <!-- Formatage du nom de la tâche -->
                                        <?php echo strtoupper(str_replace(" ", "-", htmlspecialchars($task['text']))); ?>
                                    </strong>
                                </td>
                                <td>
                                    <!-- Si pas de description -->
                                    <?php echo !empty($task['description']) ? htmlspecialchars($task['description']) : 'Aucune description'; ?>
                                </td>
                                <td>
                                    <!-- etat actuel de la tâche -->
                                    <?php echo $task['completed'] ? 'Complétée' : 'Non complétée'; ?>
                                </td>
                                <!-- Boutons d'action -->
                                <td class ="actions">
                                    <?php if (!$task['completed']): ?>
                                        <form method="POST" style="display:inline;">
                                            <button type="submit" name="complete-task" value="<?php echo $task["identifiant"]; ?>">V</button>
                                        </form>
                                        <form method="POST" style="display:inline;">
                                            <button type="submit" name="delete-task" value="<?php echo $task["identifiant"]; ?>">X</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                                <!-- Date -->
                                <td>
                                    <?php echo htmlspecialchars(($task['date_added'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <form method="POST">
                <button type="submit" name="clear-completed" class="deleteAll">Supprimer tâches complétées</button>
            </form>

            <br>
        </div>
    </div>
</body>

</html>