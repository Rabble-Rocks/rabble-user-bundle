<?php

namespace Rabble\UserBundle\Datatable;

use Rabble\DatatableBundle\Datatable\AbstractGenericDatatable;
use Rabble\DatatableBundle\Datatable\Row\Data\Column\Action\Action;
use Rabble\DatatableBundle\Datatable\Row\Data\Column\ActionDataColumn;
use Rabble\DatatableBundle\Datatable\Row\Data\Column\GenericDataColumn;
use Rabble\DatatableBundle\Datatable\Row\Heading\Column\GenericHeadingColumn;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserDatatable extends AbstractGenericDatatable
{
    private AuthorizationCheckerInterface $checker;

    public function __construct(AuthorizationCheckerInterface $checker)
    {
        $this->checker = $checker;
    }

    protected function initialize(): void
    {
        if (!$this->checker->isGranted('user.view')) {
            throw new AccessDeniedHttpException();
        }
        $this->headingColumns = [
            new GenericHeadingColumn('', false, ['style' => ['width' => 60], 'data-sortable' => 'false']),
            new GenericHeadingColumn('table.user.username', 'RabbleUserBundle'),
            new GenericHeadingColumn('table.user.first_name', 'RabbleUserBundle'),
            new GenericHeadingColumn('table.user.last_name', 'RabbleUserBundle'),
        ];
        $this->dataColumns = [
            new ActionDataColumn([
                'actions' => [
                    new Action(
                        'Routing.generate("rabble_admin_user_view", {user: data.getId()})',
                        'eye'
                    ),
                    new Action(
                        'Routing.generate("rabble_admin_user_edit", {user: data.getId()})',
                        'pencil',
                        '(is_granted(data) || is_granted("role.overrule")) && is_granted("user.edit")'
                    ),
                    new Action(
                        'Routing.generate("rabble_admin_user_delete", {user: data.getId()})',
                        'trash',
                        '(is_granted(data) || is_granted("role.overrule")) && is_granted("user.delete") && data !== get_user()',
                        [
                            'class' => 'btn-danger',
                            'data-confirm' => '?Translator.trans("user.delete_confirm", [], "RabbleUserBundle")',
                            'data-reload-datatable' => $this->getName(),
                        ]
                    ),
                ],
            ]),
            new GenericDataColumn([
                'expression' => 'data.getUsername()',
                'searchField' => 'username',
                'sortField' => 'username',
            ]),
            new GenericDataColumn([
                'expression' => 'data.getFirstName()',
                'searchField' => 'firstName',
                'sortField' => 'firstName',
            ]),
            new GenericDataColumn([
                'expression' => 'data.getLastName()',
                'searchField' => 'lastName',
                'sortField' => 'lastName',
            ]),
        ];
    }
}
