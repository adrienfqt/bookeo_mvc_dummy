<?php require_once _ROOTPATH_ . '\templates\header.php';
/** @var $totalPages */ /** @var $page */ /** @var $books */
?>

<h1>Liste complète</h1>

<div class="row text-center mb-3">
    <?php foreach($books as $book){?>
    <div class="col-md-4 my-2 d-flex">
        <div class="card">
                <img src="<?php if ($book['image'] != '' or $book['image'] != null){echo _BOOKS_IMAGES_FOLDER_.$book['image'];
                }else{echo _ASSETS_IMAGES_FOLDER_."default-book.jpg";} ?>"
                     class="card-img-top" alt="Zaï Zaï Zaï Zaï">
            <div class="card-body">
                <h5 class="card-title"><?php echo $book['title']; ?></h5>
                <p class="card-text"><?php echo substr($book['description'],0,93).". . ."; ?></p>
                <a href="index.php?controller=book&amp;action=show&amp;id=<?php echo $book['id']; ?>" class="btn btn-primary">Lire la suite</a>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<div class="row">
    <?php if ($totalPages > 1) { ?>
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <?php for ($i =1; $i <= $totalPages;$i++) { ?>
                    <li class="page-item <?php if ($i === $page) { echo "active"; } ?>"><a class="page-link" href="index.php?controller=book&amp;action=list&amp;&page=<?=$i;?>"><?=$i;?></a></li>
                <?php } ?>
            </ul>
        </nav>
    <?php } ?>
</div>

<?php require_once _ROOTPATH_ . '\templates\footer.php'; ?>