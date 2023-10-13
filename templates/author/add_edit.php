<?php require_once _TEMPLATEPATH_ . '\header.php';
/** @var $author */ /** @var $errors */ /** @var $pageTitle */
?>

    <h1><?= $pageTitle; ?></h1>

<?php if($errors) {
    foreach ($errors as $error){ ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error ?>
        </div>
    <?php }?>
<?php }?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="firstName" class="form-label">Prénom</label>
            <input type="text" class="form-control " id="firstName" name="firstName" value="<?php echo $author->getFirstName() ?>">
        </div>

        <div class="mb-3">
            <label for="lastName" class="form-label">Nom de famille</label>
            <input type="text" class="form-control " id="lastName" name="lastName" value="<?php echo $author->getLastName() ?>">
        </div>

        <div class="mb-3">
            <label for="nickName" class="form-label">Surnom</label>
            <input type="text" class="form-control " id="nickName" name="nickName" value="<?php echo $author->getNickname() ?>">
        </div>

        <input type="submit" name="saveAuthor" class="btn btn-primary" value="Enregistrer">

    </form>


<?php require_once _TEMPLATEPATH_ . '\footer.php'; ?>