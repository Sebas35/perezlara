<?php

namespace App\Controllers;

use App\Models\Client;
use App\Models\Insurance;
use App\Models\Insurer;
use App\Models\Policy;
use App\Models\Quote;
use App\Models\User;
use App\Traits\Controllers\InsurerInsuranceActive;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Mail\Mail;

class UserController extends Controller
{
    use InsurerInsuranceActive;

    public function index(): bool|array
    {
        return (new User()) -> index();
    }

    public function create(User $user): void
    {
        try {
            if (is_string($user -> create())) {
                throw new Exception('No se pudo crear el usuario');
            }
            ob_start();
            require_once view('mail/email_confirmation.php');
            $body = ob_get_clean();
            if (is_string((new Mail($user -> getEmail(), $user -> getFirstName() . ' ' . $user -> getFirstSurname(), 'Verificar correo electrónico', $body)) -> send())) {
                $user -> delete();
                throw new Exception('No pudimos crear tu cuenta');
            }
            echo json_encode('Tu cuenta ha sido creada exitosamente');
        } catch (Exception $e) {
            echo json_encode($e -> getMessage());
        }
    }

    public function createWithPermissions(User $user): void
    {
        try {
            if (is_string($user -> create())) {
                throw new Exception('No se pudo crear el usuario');
            }
            $this -> response('Usuario creado');
        } catch (Exception $e) {
            echo json_encode($e -> getMessage());
        }
    }

    public function delete(User $user): void
    {
        $user -> inactivate();
        $this -> response('Usuario eliminado');
    }

    public function view(string $view): void
    {
        $types_documents = (new User()) -> documentType();
        require_once $view;
    }

    public function dashboard(): void
    {
        $array = $this -> productsActive();
        $quote = new Quote();
        $client = new Client();
        $policy = new Policy();
        $array['recent_quotes'] = $quote -> recent();
        $array['total_clients'] = $client -> total();
        $array['total_quotes'] = $quote -> total();
        $array['total_policies'] = $policy -> total();
        $array['count_states_policies'] = $policy -> countStates();
        echo json_encode($array);
    }

    public function products(): void
    {
        echo json_encode(['insurers' => (new Insurer()) -> card(), 'insurances' => (new Insurance()) -> card()]);
    }

    public function users(string $view): void
    {
        $user = (new User());
        $types_documents = $user -> documentType();
        $roles = $user -> roles();
        require_once $view;
    }

    public function login(User $user): void
    {
        try {
            $login = $user -> login();
            if ($login === false) {
                throw new Exception('Usuario y/o contraseña incorrectos');
            }
            $payload = [
                'exp' => time() + 3600,
                'data' => $login,
            ];
            $jwt = JWT::encode($payload, $_ENV['PRIVATE_KEY'],'RS256');
            setcookie('x-token', hash('sha256',$jwt), time() + 3600, '/');
            echo json_encode(['success' => true]);
//            $decoded = JWT::decode($jwt, new Key($_ENV['PUBLIC_KEY'], 'RS256'));
        } catch (Exception $e) {
            echo json_encode(['error' => $e -> getMessage()]);
        }
    }

    public function show(User $user): bool|array
    {
        return $user -> show();
    }

    public function update(User $user, string $document, bool $all = false): void
    {
        $user -> update($document);
        $this -> response('Usuario actualizado', $all ? null : $this -> show($user));
    }
}
