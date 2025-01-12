<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\CommentRepository;
use App\Entity\Book;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Rating;
use App\Tools\FileTools;
use App\Repository\TypeRepository;
use App\Repository\AuthorRepository;
use App\Repository\RatingRepository;


class BookController extends Controller
{
    public function route(): void
    {
        try {
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'show':
                        $this->show();
                        break;
                    case 'add':
                        $this->add();
                        break;
                    case 'edit':
                        $this->edit();
                        break;
                    case 'delete':
                        $this->delete();
                        break;
                    case 'list':
                        $this->list();
                        break;
                    default:
                        throw new \Exception("Cette action n'existe pas : " . $_GET['action']);
                        break;
                }
            } else {
                throw new \Exception("Aucune action détectée");
            }
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }
    /*
    Exemple d'appel depuis l'url
        ?controller=book&action=show&id=1
    */
    protected function show()
    {
        $errors = [];

        try {
            if (isset($_GET['id'])) {

                $id = (int)$_GET['id'];
                // Charger le livre par un appel au repository findOneById
                $bookRepository = new BookRepository();
                $book = $bookRepository->findOneById($id);

                if ($book) {
                    $commentRepository = new CommentRepository();
                    $commentaire = new Comment();
                    $commentaire->setId(User::getCurrentUserId());
                    $commentaire->setBookId($id);

                    $rate = null;
                    if (isset($_POST['saveComment'])) {
                        if (!User::isLogged()) {
                            throw new \Exception("Accès refusé");
                        }else{
                            $commentaire = Comment::createAndHydrate($_POST);
                            $errors = $commentaire->validate();
                        }
                        if (empty($errors)) {
                            $commentRepository->persist($commentaire);
                        }
                    }

                    $comments = $commentRepository->findAllByBookId($id);
                    $ratingRepo = new RatingRepository();

                    $average = $ratingRepo->findAverageByBookId($id);
                    if (isset($_POST['saveRating'])) {
                        if (!User::isLogged()) {
                            throw new \Exception("Accès refusé");
                        }else{
                            $rate = Rating::createAndHydrate($_POST);
                            $errors = $commentaire->validate();

                        }if (empty($errors)) {
                            $ratingRepo->persist($rate);
                        }
                    }

                    $this->render('book/show', [
                        'book' => $book,
                        'comments' => $comments,
                        'newComment' => $commentaire,
                        'rating' => $rate,
                        'averageRate' => $average,
                        'errors' => $errors,
                        'user' => User::getCurrentUserId()
                    ]);
                } else {
                    $this->render('errors/default', [
                        'error' => 'Livre introuvable'
                    ]);
                }
            } else {
                throw new \Exception("L'id est manquant en paramètre");
            }
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function add()
    {
        $this->add_edit();
    }

    protected function edit()
    {
        try {
            if (isset($_GET['id'])) {
                $this->add_edit((int)$_GET['id']);
            } else {
                throw new \Exception("L'id est manquant en paramètre");
            }
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function add_edit($id = null)
    {

        try {
            // Cette action est réservé aux admin
            if (!User::isLogged() || !User::isAdmin()) {
                throw new \Exception("Accès refusé");
            }
            $bookRepository = new BookRepository();
            $errors = [];
            // Si on a pas d'id on est dans le cas d'une création
            if (is_null($id)) {
                $book = new Book();
            } else {
                // Si on a un id, il faut récupérer le livre
                $book = $bookRepository->findOneById($id);
                if (!$book) {
                    throw new \Exception("Le livre n'existe pas");
                }
            }

            $typeRepo = new TypeRepository();
            $types = $typeRepo->findAll();

            $authorRepo = new AuthorRepository();
            $authors = $authorRepo->findAll();

            if (isset($_POST['saveBook'])) {
                $book = Book::createAndHydrate($_POST);
                $errors = $book->validate();
                // Si pas d'erreur on peut traiter l'upload de fichier
                if (empty($errors)) {
                    $fileErrors = [];
                    // On lance l'upload de fichier
                    if (isset($_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name'] !== '') {
                        $res = FileTools::uploadImage(_BOOKS_IMAGES_FOLDER_,$_FILES['file']);
                        if (empty($res['errors'])) {
                            $book->setImage($res['fileName']);
                        } else {
                            $fileErrors = $res['errors'];
                        }
                    }
                    if (empty($fileErrors)) {
                        $bookRepository->persist($book);
                        header("Location: index.php?controller=book&action=show&id=".$book->getId());
                    } else {
                        $errors = array_merge($errors, $fileErrors);
                    }
                }
            }
            $this->render('book/add_edit', [
                'book' => $book,
                'types' => $types,
                'authors' => $authors,
                'pageTitle' => 'Ajouter un livre',
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function list()
    {
        $bookRepository = new BookRepository;

        // On récupère la page courante, si page de page on met à 1
        if (isset($_GET['page'])) {
            $page = (int)$_GET['page'];
        } else {
            $page = 1;
        }

        $totalBooks = $bookRepository->count();
        $totalPages = ceil($totalBooks / _ITEM_PER_PAGE_);
        $books = $bookRepository->findAll(_ITEM_PER_PAGE_,$page);

        $this->render('book/list', [
            'books' => $books,
            'totalPages' => $totalPages,
            'page' => $page,
        ]);
    }


    protected function delete()
    {
        try {
            // Cette action est réservé aux admin
            if (!User::isLogged() || !User::isAdmin()) {
                throw new \Exception("Accès refusé");
            }

            if (!isset($_GET['id'])) {
                throw new \Exception("L'id est manquant en paramètre");
            }
            $bookRepository = new BookRepository();

            $id = (int)$_GET['id'];

            $book = $bookRepository->findOneById($id);

            if (!$book) {
                throw new \Exception("Le livre n'existe pas");
            }
            if ($bookRepository->removeById($id)) {
                // On redirige vers la liste de livre
                header('location: index.php?controller=book&action=list&alert=delete_confirm');
            } else {
                throw new \Exception("Une erreur est survenue l'ors de la suppression");
            }

        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
