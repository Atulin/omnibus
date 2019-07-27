<?php
namespace Controllers\User;

use Core\Controller;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Models\ActivationCode;
use Models\User;

class ActivateController extends Controller
{
    /**
     * @var array
     */
    private $messages = [];

    /**
     * Render the page
     */
    public function index(): void
    {
        $this->setBaseData();
        $this->render('user/activate', ['messages' => $this->messages]);
    }

    /**
     * @param $params
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function activate($params): void
    {
        $code = ($_POST['code'] ?? $params['code']) ?? null;
        if (!empty($code)) {

            $ac = $this->em->getRepository(ActivationCode::class)->findOneBy(['code' => $code]);
//            $user = $this->em->getRepository(User::class)->findOneBy(['code' => $code]);

            if($ac !== null) {
                $this->em->remove($ac);
                $this->em->flush();
                $this->session->set('message', 'Your account has been activated successfully!');
                header('Location: /login');
                die();
            }

            $this->messages[] = 'Incorrect code.';

        } else {
            $this->messages[] = 'Code cannot be empty.';
        }

        $this->index();
    }
}
