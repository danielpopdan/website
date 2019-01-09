<?php

namespace Drupal\dct_commerce\Controller;

use Drupal\Console\Bootstrap\Drupal;
use Drupal\Core\Controller\ControllerBase;
use Drupal\dct_sessions\Service\UserSessions;
use Drupal\user\UserInterface;

class ScanTicket extends ControllerBase
{
    public function content(UserInterface $user)
    {
        $ticket = \Drupal::entityTypeManager()->getStorage('dct_commerce_ticket')->loadMultiple(
            ['redeemer' => $user->id()]
        );
        $ticket = current($ticket);
        $shirt_sizes = $user->getFieldDefinition('field_shirt_size')->getSetting('allowed_values');
        $shirt_types = $user->getFieldDefinition('field_gender')->getSetting('allowed_values');
        $render = [
            '#theme' => 'dct_scan_ticket',
            '#country' => [
                '#markup' => $user->get('field_country')->value,
            ],
            '#given_name' => [
                '#markup' => $user->get('field_first_name')->value,
            ],
            '#family_name' => [
                '#markup' => $user->get('field_last_name')->value,
            ],
            '#username' => [
                '#markup' => $user->get('field_drupal_org_username')->value,
            ],
            '#shirt_size' => [
                '#markup' => $shirt_sizes[$user->get('field_shirt_size')->value],
            ],
            '#shirt_type' => [
                '#markup' => $shirt_types[$user->get('field_gender')->value],
            ],
            '#role' => [
                '#markup' => UserSessions::getPublicUserRoles($user),
            ],
            '#current_date' => [
                '#markup' => date('d.m.Y'),
            ],
        ];
        if (!empty($ticket) && !empty($ticket->getCode())) {
            $render['#code']['#markup'] = $ticket->getCode();
            if (empty($user->get('field_attended')->value)) {
                $render['#validate'] = \Drupal::formBuilder()->getForm('Drupal\dct_commerce\Form\ValidateTicketForm');
            } else {
                if (!empty(\Drupal::request()->get('status')) && \Drupal::request()->get('status') == 'success') {
                    $render['#validate'] = [
                        '#prefix' => '<h2>',
                        '#markup' => t('The ticket was successfully scanned!'),
                        '#sufix' => '</h2>',
                    ];
                } else {
                    $render['#validate'] = [
                        '#prefix' => '<h2>',
                        '#markup' => t('This ticket was already scanned!'),
                        '#sufix' => '</h2>',
                    ];
                }
            }
        }

        return $render;
    }
}
