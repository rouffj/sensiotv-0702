<?php


namespace App\Controller;

use App\Entity\Movie;
use App\OmdbApi;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;

class MovieController extends AbstractController
{
    private OmdbApi $omdbApi;

    public function __construct()
    {
        $httpClient = new CurlHttpClient(['verify_host' => false, 'verify_peer' => false]);
        $this->omdbApi = new OmdbApi($httpClient, '28c5b7b1', 'https://www.omdbapi.com');
    }

    /**
     * @Route("/movie", name="movie")
     */
    public function index(): Response
    {
        return $this->render('movie/index.html.twig', [
            'controller_name' => 'MovieController',
        ]);
    }

    /**
     * @Route("/movie/latest", name="movie_latest")
     */
    public function latest(MovieRepository $movieRepository): Response
    {
        $movies = $movieRepository->findBy([], ['id' => 'DESC']);

        return $this->render('movie/latest.html.twig', [
            'movies' => $movies,
        ]);
    }

    /**
     * @Route("/movie/{imdbId}/import", name="movie_import")
     */
    public function import($imdbId, EntityManagerInterface $manager): Response
    {
        $movieData = $this->omdbApi->requestOneById($imdbId);

        $movie = Movie::fromApi($movieData);
        $manager->persist($movie);
        $manager->flush();

        return $this->redirectToRoute('movie_latest');
    }

    /**
     * @Route("/movie/search", name="movie_search")
     */
    public function search(Request $request): Response
    {
        $keyword = $request->query->get('keyword', 'Sun');
        $movies = $this->omdbApi->requestAllBySearch($keyword);
        dump($this->omdbApi, $movies);

        return $this->render('movie/search.html.twig', [
            'movies' => $movies,
            'keyword' => $keyword,
        ]);
    }

    /**
     * @Route("/movie/{id}", name="movie_show", requirements={"id": "\d+"})
     */
    public function show(int $id, MovieRepository $movieRepository): Response
    {
        $movie = $movieRepository->findOneById($id);

        return $this->render('movie/show.html.twig', [
            'id' => $id,
            'movie' => $movie,
        ]);
    }
}