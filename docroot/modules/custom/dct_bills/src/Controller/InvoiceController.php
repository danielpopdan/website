<?php

namespace Drupal\dct_bills\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Drupal\dct_sessions\Service\SessionProposalService;
use Drupal\dct_sessions\Service\UserSessions;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class InvoiceController.
 *
 * @package Drupal\dct_bills\Controller
 */
class InvoiceController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The dct_sessions.public_user_roles service.
   *
   * @var \Drupal\dct_sessions\Service\UserSessions
   */
  protected $userSessions;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, AccountProxyInterface $current_user, UserSessions $userSessions) {
    $this->entityTypeManager = $entity_type_manager;
    $this->currentUser = $current_user;
    $this->userSessions = $userSessions;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('current_user'),
      $container->get('dct_sessions.public_user_roles')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getInvoices() {
    $orders = $this->entityTypeManager->getStorage('commerce_order')
      ->loadByProperties(['uid' => $this->currentUser->id()]);
    $user = $this->entityTypeManager->getStorage('user')
      ->load($this->currentUser->id());
    $user_picture = $this->entityTypeManager->getViewBuilder('user')
      ->viewField($user->get('user_picture'), 'full');
    $invoices = [];
    foreach ($orders as $order) {
      if (!empty($order->get('field_bill')->target_id)) {
        $file = $this->entityTypeManager->getStorage('file')
          ->load($order->get('field_bill')->target_id);

        $invoices[] = [
          'title' => [
            '#markup' => date('m/d/Y', $order->getCompletedTime()) . ' - ' . round($order->getTotalPrice()
              ->getNumber(), 2) . ' ' . $order->getTotalPrice()
              ->getCurrencyCode(),
          ],
          'file' => [
            '#markup' => file_create_url($file->getFileUri()),
          ],
        ];
      }
    }

    return [
      '#theme' => 'dct_bill_my_invoices',
      '#user' => $user,
      '#user_picture' => $user_picture,
      '#user_roles' => $this->userSessions->getPublicUserRoles($this->currentUser),
      '#invoices' => $invoices,
      '#cache' => [
        'max-age' => 0,
      ],
    ];
  }

}
