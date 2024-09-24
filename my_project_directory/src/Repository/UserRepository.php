<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getAllUsers(): array {
        $userDb = $this->findAll();

        return $userDb;
    }

    public function getUserById(int $userId): User | string {
        $userDb = $this->findOneBy(["id"=> $userId]);
        if(empty($userDb)) {
            return 'No se encontró el usuerio';
        }
        return $userDb;
    }

    public function createUser(User $newData): string|bool {

        $userEmail = $newData->getEmail();
        $searchUser = $this->findOneBy(['email'=> $userEmail]);
        if(empty($searchUser)) {
            if(empty($newData->getName())) {
                return 'No hay valor para name';
            }
            if(empty($newData->getEmail())) {
                return 'No hay valor para email';
            }
            if(empty($newData->getPassword())) {
                return 'No hay valor para password';
            }
            if(empty($newData->getRole())) {
                return 'No hay valor para role';
            }
        } else {
            return "Ya hay un usuario con este email";
        }
        $this->getEntityManager()->persist($newData);
        $this->getEntityManager()->flush();
        return 'User creado correctamente';
    }

    public function updateUser(User $newData): string {
        // Busca el usuario por ID, que se asume que es único
        $userFound = $this->find($newData->getId());
        
        if (empty($userFound)) {
            return 'No existe el user';
        } else {
            // Campos a actualizar
            $fields = [
                'name' => $newData->getName(),
                'email' => $newData->getEmail(),
                'password' => $newData->getPassword(),
                'role' => $newData->getRole()
            ];
            
            foreach ($fields as $field => $value) {
                if (!empty($value)) {
                    // Usa la reflexión para establecer el valor en el usuario existente
                    $setter = 'set' . ucfirst($field);
                    if (method_exists($userFound, $setter)) {
                        $userFound->$setter($value);
                    }
                }
            }
    
            // Guardar los cambios en la base de datos
            $this->getEntityManager()->flush(); // Persistir cambios
    
            return 'Usuario actualizado con éxito'; // Mensaje de éxito
        }
    }
    

    public function deleteUser(int $userId): bool {
        $userToFound = $this->findOneBy(['id'=> $userId]);
        if(empty($userToFound)) {
            return false;
        } else {
            $this->getEntityManager()->remove($userToFound);
            $this->getEntityManager()->flush();
            return true;
        }
    }

    public function signup (User $user): bool {
        $userToFound = $this->findOneBy(['email'=> $user->getEmail()]);
        if(empty($userToFound)) {
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
            return true;
        } else {
            return false;
        }
    }
    
    public function login(string $email, string $password): bool|string {
        $userToFound = $this->findOneBy(['email'=> $email, 'password'=> $password]);
        if(empty($userToFound)) {
            return false;
        } else {
            $userId = $userToFound->getId();
            if(empty($userId)) {
                return false;
            }
            return $userId;
        }
    }
}
