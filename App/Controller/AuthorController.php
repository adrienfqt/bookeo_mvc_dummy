<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\User;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\TypeRepository;
use App\Repository\UserRepository;
use App\Tools\FileTools;

class AuthorController extends Controller
{
    public function route(): void
    {
        try {
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'index':
                        $this->index();
                        break;
                    case 'delete':
                        $this->delete();
                        break;
                    case 'add':
                        $this->add();
                        break;
                    case 'edit':
                        $this->edit();
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


    protected function index()
    {
        $errors = [];
        if (!User::isLogged()) {
            throw new \Exception("Accès refusé");
        }else{
            $authorRepo = new AuthorRepository();
            $authors = $authorRepo->findAll();
        }
        var_dump($authors);
        $this->render('author/index', [
            'authors' => $authors,
            'errors' => $errors,
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
            $authorRepo = new AuthorRepository();

            $id = (int)$_GET['id'];

            if ($authorRepo->removeById($id)) {
                // On redirige vers la liste de livre
                header('location: index.php?controller=author&action=index&alert=delete_confirm');
            } else {
                throw new \Exception("Une erreur est survenue l'ors de la suppression");
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
            $authorRep = new AuthorRepository();
            $errors = [];
            // Si on a pas d'id on est dans le cas d'une création
            if (is_null($id)) {
                $author = new Author();
            } else {
                // Si on a un id, il faut récupérer le livre
                $author = $authorRep->findOneById($id);
                if (!$author) {
                    throw new \Exception("L'auteur n'existe pas");
                }
            }

            if (isset($_POST['saveAuthor'])) {
                $author = Author::createAndHydrate($_POST);
                // Si pas d'erreur on peut traiter l'upload de fichier
                $authorRep->persist($author);
                header("Location: index.php?controller=author&action=index&alert=addConfirm");
            }
            $this->render('author/add_edit', [
                'pageTitle' => 'Ajouter un auteur',
                'author' => $author,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }
}