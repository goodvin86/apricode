<?php

namespace App\Controller;

use App\Entity\Game;
use App\Form\GameType;
use App\Repository\GameRepository;
use App\Repository\GenreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/game')]
class GameController extends AbstractController
{
    //получаем все игры
    #[Route('/', name: 'app_game')]
    public function indexGame(Request $request, GameRepository $gameRepository, GenreRepository $genreRepository): Response
    {
        //выводим игры выбранного жанра
        if ($request->query->get('preview')) {
            $genreId = $request->query->get('q');
            if($genreId  == '0'){
                return $this->render('game/_searchPreview.html.twig', [
                    'games' => $gameRepository->findAllGame(),
                ]);
            }
            else{
                $games = $gameRepository->findGameByGenre($genreId);
                return $this->render('game/_searchPreview.html.twig', [
                    'games' => $games,
                ]);
            }
        }

        //выводим все игры
        return $this->render('game/index.html.twig', [
            'games' => $gameRepository->findAllGame(),
            'genres' => $genreRepository->findAll(),
        ]);
    }

    //добавляем новую игру
    #[Route('/new', name: 'app_game_new', methods: ['GET', 'POST'])]
    public function newGame(Request $request, GameRepository $gameRepository): Response
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $gameRepository->save($game, true);

            return $this->redirectToRoute('app_game', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('game/form.html.twig', [
            'game' => $game,
            'form' => $form,
            'title'=>'Новая игра',
        ]);
    }

    //редактируем игру
    #[Route('/edit/{id}', name: 'app_game_edit', methods: ['GET', 'POST'])]
    public function editGame(Request $request, Game $game, GameRepository $gameRepository): Response
    {
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $gameRepository->save($game, true);
            return $this->redirectToRoute('app_game', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('game/form.html.twig', [
            'user' => $game,
            'form' => $form,
            'title'=>'Редактирование игры',
        ]);
    }

    //удаляем игру
    #[Route('/delete/{id}', name: 'app_game_delete', methods: ['GET'])]
    public function deleteGame(string $id, Request $request, Game $game, GameRepository $gameRepository): Response
    {
        $gameRepository->remove($game, true);
        die(json_encode(['text' => 'Игра успешно удалена из базы',
            'id'=>$id,
            'title'=>'Результат',
            'button'=>'Отлично!',
            'color'=>'#04bf23'
        ]));
    }
}
