<head>
    <link rel="stylesheet" href="assets/css/classement.css">
<body>

<?php
    include "partials/header.php"
?>
    <h1>Classement</h1>
    <div id='classement'>
    <form method="get">
        <div class="filter">
            <input id="champ" name="username" type="text" placeholder="Pseudo" value="<?php echo htmlspecialchars($_GET['username'] ?? ''); ?>" />
        </div>
        <button id="Rechercher" type="submit">Rechercher</button>
    </form> 

    <section>
        <table id='tableau'>
            <thead>
                <th>utilisateur</th>
                <th>argent</th>
            </thead>



            <?php
            require 'utils/database.php';
            $db = Database::getInstance();
            $conn = $db->getConnection();
            $username = $_GET['username'] ?? '';


            try{
                $select = "SELECT username, balance FROM users";

                if (!empty($username)) {
                    $select .= " WHERE username LIKE :username";
                }

                $select .= " ORDER BY balance DESC";


                $stmt = $conn->prepare($select);
                
                if (!empty($username)) {
                    $stmt->execute([':username' => "%$username%"]);
                } else {
                    $stmt->execute();
                }


                while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            
                    echo "<tr> 
                    <td>" . htmlspecialchars($data['username']) . "</td>
                    <td>" . htmlspecialchars($data['balance']) . "</td>
                    </tr>";

                }
            }
            catch (PDOException $e) {
                echo json_encode(["success" => false, "error" => $e->getMessage()]);
            }
            ?>
        </table>
    </section>
    </div>
</body>
