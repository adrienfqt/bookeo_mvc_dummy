<?php require_once _TEMPLATEPATH_ . '\header.php';
/** @var $types @var*/  /** @var $authors */ /** @var $errors */ /** @var $book */
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
        <label for="title" class="form-label">Titre</label>
        <input type="text" class="form-control " id="title" name="title" value="<?php echo $book->getTitle() ?>">

    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="3"><?php echo $book->getDescription() ?></textarea>
    </div>

    <div class="mb-3">
        <label for="type" class="form-label">Type</label>

        <select name="type_id" id="type" class="form-select">
            <?php foreach ($types as $type){?>
                <option <?php if($type->getId() == $book->getTypeId()){echo 'selected = "selected"';} ?>value="<?php echo $type->getId() ?>"><?php echo $type->getName() ?></option>
        <?php
        }?>
        </select>
    </div>

    <div class="mb-3">
        <label for="author" class="form-label">Auteur</label>
        <select name="author_id" id="author" class="form-select">
            <?php foreach ($authors as $author){?>
                <option <?php if($author->getId() == $book->getAuthorId()){echo 'selected = "selected"';} ?> value="<?php echo $author->getId() ?>"><?php echo $author->getFirstName()." ".$author->getLastName() ?></option>
                <?php
            }?>
        </select>
    </div>

    <input type="hidden" name="image" value="">
    <div class="mb-3">
        <label for="file" class="form-label">Image</label>
        <input type="file" name="file" id="file" class="form-control ">
    </div>

    <input type="submit" name="saveBook" class="btn btn-primary" value="Enregistrer">

</form>


<?php require_once _TEMPLATEPATH_ . '\footer.php'; ?>