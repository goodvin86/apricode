<?php

namespace App\Controller;

use App\Entity\Developer;
use App\Form\DeveloperType;
use App\Repository\DeveloperRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/developer')]
class DeveloperController extends AbstractController
{
    //получаем всех разрботчиков
    #[Route('/', name: 'app_developer')]
    public function indexDeveloper(Request $request, DeveloperRepository $developerRepository): Response
    {
        return $this->render('developer/index.html.twig', [
            'developers' => $developerRepository->findAll(),
        ]);
    }

    //добавляем нового разработчика
    #[Route('/new', name: 'app_developer_new', methods: ['GET', 'POST'])]
    public function newDeveloper(Request $request, DeveloperRepository $developerRepository): Response
    {
        $developer = new Developer();
        $form = $this->createForm(DeveloperType::class, $developer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $developerRepository->save($developer, true);
            return $this->redirectToRoute('app_developer', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('developer/form.html.twig', [
            'developer' => $developer,
            'form' => $form,
            'title'=>'Новая студия разработки',
        ]);
    }

    //редактируем разработчика
    #[Route('/edit/{id}', name: 'app_developer_edit', methods: ['GET', 'POST'])]
    public function editDeveloper(Request $request, Developer $developer, DeveloperRepository $developerRepository): Response
    {
        $form = $this->createForm(DeveloperType::class, $developer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $developerRepository->save($developer, true);
            return $this->redirectToRoute('app_developer', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('developer/form.html.twig', [
            'developer' => $developer,
            'form' => $form,
            'title'=>'Редактирование студии разработки',
        ]);
    }

    //удаляем разработчика
    #[Route('/delete/{id}', name: 'app_developer_delete', methods: ['GET'])]
    public function deleteDeveloper(string $id, Request $request, Developer $developer, DeveloperRepository $developerRepository): Response
    {
        $gamesWithThisDeveloper = $developerRepository->findGamesWithThisDeveloper($id);

        if ($gamesWithThisDeveloper) {
            die(json_encode(['text' => 'Сперва удалите все игры данной студии разработки',
                'title'=>'Ошибка',
                'button'=>'Хорошо',
                'color'=>'#3085d6'
            ]));
        }
        else {
            $developerRepository->remove($developer, true);
            die(json_encode(['text' => 'Студия разработки успешно удалена из базы',
                'id'=>$id,
                'title'=>'Результат',
                'button'=>'Отлично!',
                'color'=>'#04bf23'
            ]));
        }
    }
}
