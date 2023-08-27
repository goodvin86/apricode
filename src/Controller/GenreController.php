<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Form\GenreType;
use App\Repository\GenreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/genre')]
class GenreController extends AbstractController
{
    //получаем все жанры
    #[Route('/', name: 'app_genre')]
    public function indexGenre(Request $request, GenreRepository $genreRepository): Response
    {
        return $this->render('genre/index.html.twig', [
            'genres' => $genreRepository->findAll(),
        ]);
    }

    //добавляем новый жанр
    #[Route('/new', name: 'app_genre_new', methods: ['GET', 'POST'])]
    public function newGenre(Request $request, GenreRepository $genreRepository): Response
    {
        $genre = new Genre();
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $genreRepository->save($genre, true);

            return $this->redirectToRoute('app_genre', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('genre/form.html.twig', [
            'genre' => $genre,
            'form' => $form,
            'title'=>'Новый жанр',
        ]);
    }

    //редактируем жанр
    #[Route('/edit/{id}', name: 'app_genre_edit', methods: ['GET', 'POST'])]
    public function editGenre(Request $request, Genre $genre, GenreRepository $genreRepository): Response
    {
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $genreRepository->save($genre, true);
            return $this->redirectToRoute('app_genre', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('genre/form.html.twig', [
            'genre' => $genre,
            'form' => $form,
            'title'=>'Редактирование жанра',
        ]);
    }

    //удаляем жанр
    #[Route('/delete/{id}', name: 'app_genre_delete', methods: ['GET'])]
    public function deleteGenre(string $id, Request $request, Genre $genre, GenreRepository $genreRepository): Response
    {
        $gamesWithThisGenre = $genreRepository->findGamesWithThisGenre($id);

        if ($gamesWithThisGenre) {
            die(json_encode(['text' => 'Сперва удалите все игры данного жанра',
                'title'=>'Ошибка',
                'button'=>'Хорошо',
                'color'=>'#3085d6'
            ]));
        }
        else {
            $genreRepository->remove($genre, true);
            die(json_encode(['text' => 'Жанр успешно удален из базы',
                'id'=>$id,
                'title'=>'Результат',
                'button'=>'Отлично!',
                'color'=>'#04bf23'
            ]));
        }
    }
}
