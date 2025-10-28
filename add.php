<?php include 'db.php'; ?>

<?php
if ($_SERVER["REQUEST_METHOD"]==="POST"){
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];

$sql = "INSERT INTO contacts (nom, email, telephone) VALUES (?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$nom, $email, $telephone]);

header("Location:index.php");
exit();
}

?>
<form method="POST">
<label>Nom :</label><br>
<input type="text" name="nom" required><br>
<label>Email :</label><br>
<input type="email" name="email" required><br>
<label>Téléphone :</label><br>
<input type="text" name="telephone"><br><br>
<button type="submit">Ajouter</button>
</form>
<br>
<a href="index.php">← Retour à la liste</a>