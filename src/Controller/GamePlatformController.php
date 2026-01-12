<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\AplicacionRepository;
use App\Repository\UserRepository;
use App\Repository\JuegoRepository;
use App\Repository\PartidaRepository;
use App\Entity\User;
use App\Entity\Partida;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

final class GamePlatformController extends AbstractController
{
    #[Route('/game/platform', name: 'app_game_platform')]
    public function index(JuegoRepository $juegoRepository): Response
    {
        // Obtener todos los juegos activos que tienen una aplicación con apiKey
        $juegos = $juegoRepository->createQueryBuilder('j')
            ->innerJoin('j.aplicacion', 'a')
            ->where('j.estado = :estado')
            ->andWhere('a.estado = :estadoApp')
            ->andWhere('a.apiKey IS NOT NULL')
            ->setParameter('estado', true)
            ->setParameter('estadoApp', true)
            ->getQuery()
            ->getResult();

        return $this->render('game_platform/index.html.twig', [
            'juegos' => $juegos,
        ]);
    }

    // Prueba basica

    #[Route('/api/prueba', name: 'prueba')]
    public function prueba(): Response
    {
        return $this->json(['message' => 'API is working']);
    }

    // Conexion

    #[Route('/api/conexion', name: 'api_conexion', methods: ['POST'])]
    public function conexion(Request $request, AplicacionRepository $aplicacionRepository): JsonResponse
    {
        // Obtener los datos del request
        $data = json_decode($request->getContent(), true);
        
        // Validar que se envió la API-KEY
        if (!isset($data['api_key']) || empty($data['api_key'])) {
            return $this->json([
                'success' => false,
                'message' => 'API-KEY no proporcionada',
                'data' => '',
            ], 400);
        }

        $apiKey = $data['api_key'];

        // Buscar la aplicación por API-KEY
        $aplicacion = $aplicacionRepository->findOneBy(['apiKey' => $apiKey]);

        // Validar si existe la aplicación y está activa
        if (!$aplicacion || !$aplicacion->isEstado()) {
            return $this->json([
                'success' => false,
                'message' => 'Conexión no permitida, API-KEY inválida',
                'data' => '',
            ], 401);
        }

        // Generar un token de acceso (por ahora un token simple)
        $accessToken = bin2hex(random_bytes(32));

        // Respuesta exitosa
        return $this->json([
            'success' => true,
            'message' => 'Conexión exitosa',
            'data' => [
                'access_token' => $accessToken,
            ],
        ], 200);
    }

    // Login de usuario
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request, 
        AplicacionRepository $aplicacionRepository,
        UserRepository $userRepository,
        JuegoRepository $juegoRepository,
        PartidaRepository $partidaRepository,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse
    {
        // Obtener los datos del request
        $data = json_decode($request->getContent(), true);
        
        // Validar que se envió la API-KEY
        if (!isset($data['api_key']) || empty($data['api_key'])) {
            return $this->json([
                'success' => false,
                'message' => 'Conexión no permitida, API-KEY inválida',
                'data' => '',
            ], 400);
        }

        // Validar la API-KEY
        $aplicacion = $aplicacionRepository->findOneBy(['apiKey' => $data['api_key']]);
        if (!$aplicacion || !$aplicacion->isEstado()) {
            return $this->json([
                'success' => false,
                'message' => 'Conexión no permitida, API-KEY inválida',
                'data' => '',
            ], 401);
        }

        // Validar que se enviaron usuario y password
        if (!isset($data['usuario']) || !isset($data['password'])) {
            return $this->json([
                'success' => false,
                'message' => 'Usuario o contraseña no proporcionados',
                'data' => '',
            ], 400);
        }

        // Buscar el usuario por email
        $user = $userRepository->findOneBy(['email' => $data['usuario']]);
        
        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'Usuario o contraseña incorrectos',
                'data' => '',
            ], 401);
        }

        // Verificar la contraseña
        if (!$passwordHasher->isPasswordValid($user, $data['password'])) {
            return $this->json([
                'success' => false,
                'message' => 'Usuario o contraseña incorrectos',
                'data' => '',
            ], 401);
        }

        // Generar un token único para el usuario
        $usuarioToken = bin2hex(random_bytes(20));

        // Obtener todos los juegos de la aplicación
        $juegos = $juegoRepository->findBy(['aplicacion' => $aplicacion, 'estado' => true]);
        
        $listadoJuegos = [];
        foreach ($juegos as $juego) {
            // Contar las partidas del usuario para este juego
            $numPartidas = $partidaRepository->count(['usuario' => $user, 'juego' => $juego]);
            
            $listadoJuegos[] = [
                'juego' => $juego->getNombre(),
                'Partidas' => (string)$numPartidas,
                'token' => $juego->getTokenJuego()
            ];
        }

        // Respuesta exitosa
        return $this->json([
            'success' => true,
            'message' => 'Usuario válido',
            'data' => [
                'usuario_token' => $usuarioToken,
                'Listado juegos' => $listadoJuegos
            ],
        ], 200);
    }

    // Registro usuario
    #[Route('/api/registro', name: 'api_registro', methods: ['POST'])]
    public function registro(
        Request $request, 
        AplicacionRepository $aplicacionRepository,
        UserRepository $userRepository,
        JuegoRepository $juegoRepository,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        // Obtener los datos del request
        $data = json_decode($request->getContent(), true);
        
        // Validar que se envió la API-KEY
        if (!isset($data['api_key']) || empty($data['api_key'])) {
            return $this->json([
                'success' => false,
                'message' => 'Conexión no permitida, API-KEY inválida',
                'data' => '',
            ], 400);
        }

        // Validar la API-KEY
        $aplicacion = $aplicacionRepository->findOneBy(['apiKey' => $data['api_key']]);
        if (!$aplicacion || !$aplicacion->isEstado()) {
            return $this->json([
                'success' => false,
                'message' => 'Conexión no permitida, API-KEY inválida',
                'data' => '',
            ], 401);
        }

        // Validar que se enviaron los datos necesarios
        if (!isset($data['nombre']) || !isset($data['email']) || !isset($data['password'])) {
            return $this->json([
                'success' => false,
                'message' => 'Error en el registro del nuevo usuario',
                'data' => 'Faltan datos obligatorios (nombre, email, password)',
            ], 400);
        }

        // Verificar si el usuario ya existe
        $existingUser = $userRepository->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return $this->json([
                'success' => false,
                'message' => 'Error en el registro del nuevo usuario',
                'data' => 'La cuenta ya está dada de alta',
            ], 409);
        }

        // Crear el nuevo usuario
        $user = new User();
        $user->setEmail($data['email']);
        $user->setNombre($data['nombre']);
        $user->setRoles(['ROLE_USER']); // Establecer rol predeterminado
        
        // Hash de la contraseña
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);
        
        // Guardar el usuario
        try {
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error en el registro del nuevo usuario',
                'data' => 'Error al guardar en la base de datos',
            ], 500);
        }

        // Generar un token único para el usuario
        $usuarioToken = bin2hex(random_bytes(20));

        // Obtener todos los juegos de la aplicación
        $juegos = $juegoRepository->findBy(['aplicacion' => $aplicacion, 'estado' => true]);
        
        $listadoJuegos = [];
        foreach ($juegos as $juego) {
            $listadoJuegos[] = [
                'juego' => $juego->getNombre(),
                'Partidas' => '0',
                'token' => $juego->getTokenJuego()
            ];
        }

        // Respuesta exitosa
        return $this->json([
            'success' => true,
            'message' => 'Usuario registrado',
            'data' => [
                'usuario_token' => $usuarioToken,
                'Listado juegos' => $listadoJuegos
            ],
        ], 201);
    }

    // Informacion juego
    #[Route('/api/juego', name: 'api_juego', methods: ['POST'])]
    public function juego(
        Request $request, 
        AplicacionRepository $aplicacionRepository,
        JuegoRepository $juegoRepository,
        PartidaRepository $partidaRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        // Obtener los datos del request
        $data = json_decode($request->getContent(), true);
        
        // Validar que se envió la API-KEY
        if (!isset($data['api_key']) || empty($data['api_key'])) {
            return $this->json([
                'success' => false,
                'message' => 'Conexión no permitida, API-KEY inválida',
                'data' => '',
            ], 400);
        }

        // Validar la API-KEY
        $aplicacion = $aplicacionRepository->findOneBy(['apiKey' => $data['api_key']]);
        if (!$aplicacion || !$aplicacion->isEstado()) {
            return $this->json([
                'success' => false,
                'message' => 'Conexión no permitida, API-KEY inválida',
                'data' => '',
            ], 401);
        }

        // Validar que se envió el token del juego (token_usuario es opcional para ver ranking público)
        if (!isset($data['token_juego'])) {
            return $this->json([
                'success' => false,
                'message' => 'Error, faltan datos requeridos',
                'data' => 'Se requiere token_juego',
            ], 400);
        }

        $tokenUsuarioProvided = isset($data['token_usuario']) && !empty($data['token_usuario']);

        // Buscar el juego por token
        $juego = $juegoRepository->findOneBy(['tokenJuego' => $data['token_juego']]);
        
        if (!$juego || !$juego->isEstado()) {
            return $this->json([
                'success' => false,
                'message' => 'Error, no hay registro de ese juego',
                'data' => 'Error en el registro del juego',
            ], 404);
        }

        // Obtener el top 10 de jugadores con sus mejores puntuaciones
        $connection = $entityManager->getConnection();
        
        $sql = '
            SELECT u.nombre as jugador, MAX(p.puntos) as Puntos
            FROM partida p
            INNER JOIN user u ON p.usuario_id = u.id
            WHERE p.juego_id = :juegoId
            GROUP BY u.id, u.nombre
            ORDER BY MAX(p.puntos) DESC
            LIMIT 10
        ';
        
        $stmt = $connection->prepare($sql);
        $stmt->bindValue('juegoId', $juego->getId());
        $result = $stmt->executeQuery();
        $topJugadores = $result->fetchAllAssociative();
        
        // Formatear el listado de jugadores
        $listadoJugadores = [];
        foreach ($topJugadores as $jugador) {
            $listadoJugadores[] = [
                'jugador' => $jugador['jugador'],
                'Puntos' => (string)$jugador['Puntos']
            ];
        }

        // Si se proporcionó token_usuario, obtener los puntos del usuario
        $puntosUsuario = "0";
        if ($tokenUsuarioProvided) {
            $usuario = $userRepository->findOneBy(['email' => $data['token_usuario']]);
            if ($usuario) {
                $sqlUser = '
                    SELECT MAX(p.puntos) as PuntosUser
                    FROM partida p
                    WHERE p.juego_id = :juegoId AND p.usuario_id = :userId
                ';
                $stmtUser = $connection->prepare($sqlUser);
                $stmtUser->bindValue('juegoId', $juego->getId());
                $stmtUser->bindValue('userId', $usuario->getId());
                $resUser = $stmtUser->executeQuery();
                $row = $resUser->fetchAssociative();
                $puntosUsuario = $row && $row['PuntosUser'] !== null ? (string)$row['PuntosUser'] : "0";
            }
        }

        // Respuesta exitosa
        return $this->json([
            'success' => true,
            'message' => 'Listado de resultados del juego',
            'data' => [
                'Puntos usuario' => $puntosUsuario,
                'Listado jugadores' => $listadoJugadores
            ],
        ], 200);
    }

    // Grabar partida finalizada
    #[Route('/api/juego/guardar', name: 'api_juego_guardar', methods: ['POST'])]
    public function guardarPartida(
        Request $request, 
        AplicacionRepository $aplicacionRepository,
        JuegoRepository $juegoRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        // Obtener los datos del request
        $data = json_decode($request->getContent(), true);
        
        // Validar que se envió la API-KEY
        if (!isset($data['api_key']) || empty($data['api_key'])) {
            return $this->json([
                'success' => false,
                'message' => 'Conexión no permitida, API-KEY inválida',
                'data' => '',
            ], 400);
        }

        // Validar la API-KEY
        $aplicacion = $aplicacionRepository->findOneBy(['apiKey' => $data['api_key']]);
        if (!$aplicacion || !$aplicacion->isEstado()) {
            return $this->json([
                'success' => false,
                'message' => 'Conexión no permitida, API-KEY inválida',
                'data' => '',
            ], 401);
        }

        // Validar que se enviaron los datos requeridos
        if (!isset($data['token_usuario']) || !isset($data['token_juego']) || !isset($data['puntos'])) {
            return $this->json([
                'success' => false,
                'message' => 'Error, faltan datos requeridos',
                'data' => 'Se requieren token_usuario, token_juego y puntos',
            ], 400);
        }

        // Buscar el juego por token
        $juego = $juegoRepository->findOneBy(['tokenJuego' => $data['token_juego']]);
        
        if (!$juego || !$juego->isEstado()) {
            return $this->json([
                'success' => false,
                'message' => 'Error, no hay registro de ese juego',
                'data' => 'Error en el registro del juego',
            ], 404);
        }

        // Buscar usuario por email enviado en token_usuario
        $userEmail = $data['token_usuario'];
        $usuario = $userRepository->findOneBy(['email' => $userEmail]);
        
        if (!$usuario) {
            return $this->json([
                'success' => false,
                'message' => 'Error, usuario no encontrado',
                'data' => 'No se pudo identificar al usuario con ese email',
            ], 404);
        }

        // Crear una nueva partida
        $partida = new Partida();
        $partida->setUsuario($usuario);
        $partida->setJuego($juego);
        $partida->setPuntos((int)$data['puntos']);
        
        try {
            $entityManager->persist($partida);
            $entityManager->flush();
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Error al guardar la partida',
                'data' => 'Error en la base de datos',
            ], 500);
        }

        // Obtener el top 10 actualizado
        $connection = $entityManager->getConnection();
        
        $sql = '
            SELECT u.nombre as jugador, MAX(p.puntos) as Puntos
            FROM partida p
            INNER JOIN user u ON p.usuario_id = u.id
            WHERE p.juego_id = :juegoId
            GROUP BY u.id, u.nombre
            ORDER BY MAX(p.puntos) DESC
            LIMIT 10
        ';
        
        $stmt = $connection->prepare($sql);
        $stmt->bindValue('juegoId', $juego->getId());
        $result = $stmt->executeQuery();
        $topJugadores = $result->fetchAllAssociative();
        
        // Formatear el listado de jugadores
        $listadoJugadores = [];
        foreach ($topJugadores as $jugador) {
            $listadoJugadores[] = [
                'jugador' => $jugador['jugador'],
                'Puntos' => (string)$jugador['Puntos']
            ];
        }

        // Respuesta exitosa
        return $this->json([
            'success' => true,
            'message' => 'Listado de resultados del juego',
            'data' => [
                'usuario_token' => $data['token_usuario'],
                'Listado jugadores' => $listadoJugadores
            ],
        ], 200);
    }

}