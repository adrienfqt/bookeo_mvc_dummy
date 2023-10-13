<?php require_once _ROOTPATH_ . '\templates\header.php';
/** @var $authors */
?>

<h1>Liste complète</h1>

<div class="row text-center mb-3">
    <?php foreach($authors as $author){?>
        <div class="col-md-4 my-2 d-flex">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $author->getFirstName() ." ". $author->getLastName() ; ?></h5>
                    <a href="index.php?controller=author&amp;action=edit&amp;id=<?php echo $author->getId(); ?>" class="btn btn-primary">Modifier</a>
                    <a href="index.php?controller=author&amp;action=delete&amp;id=<?php echo $author->getId() ?>" class="btn btn-primary">Supprimer</a>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<a href="index.php?controller=author&amp;action=add&amp;" class="btn btn-primary">Ajouter un auteur</a>

<?php require_once _ROOTPATH_ . '\templates\footer.php'; ?>
