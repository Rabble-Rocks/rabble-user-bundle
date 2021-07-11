<?php

namespace Rabble\UserBundle\Datatable;

use Rabble\DatatableBundle\Datatable\AbstractGenericDatatable;
use Rabble\DatatableBundle\Datatable\Row\Data\Column\Action\Action;
use Rabble\DatatableBundle\Datatable\Row\Data\Column\ActionDataColumn;
use Rabble\DatatableBundle\Datatable\Row\Data\Column\GenericDataColumn;
use Rabble\DatatableBundle\Datatable\Row\Heading\Column\GenericHeadingColumn;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class RoleDatatable extends AbstractGenericDatatable
{
    private AuthorizationCheckerInterface $checker;

    public function __construct(AuthorizationCheckerInterface $checker)
    {
        $this->checker = $checker;
    }

    protected function initialize(): void
    {
        if (!$this->checker->isGranted('role.view')) {
            throw new AccessDeniedHttpException();
        }
        $this->headingColumns = [
            new GenericHeadingColumn('', false, ['style' => ['width' => 60], 'data-sortable' => 'false']),
            new GenericHeadingColumn('table.role.name', 'RabbleUserBundle'),
        ];
        $this->dataColumns = [
            new ActionDataColumn([
                'actions' => [
                    new Action(
                        'Routing.generate("rabble_admin_role_edit", {role: data.getId()})',
                        'pencil',
                        '(is_granted(data.getRoleName()) || is_granted("role.overrule")) && is_granted("role.edit")'
                    ),
                    new Action(
                        'Routing.generate("rabble_admin_role_delete", {role: data.getId()})',
                        'trash',
                        '(is_granted(data.getRoleName()) || is_granted("role.overrule")) && is_granted("role.delete")',
                        [
                            'class' => 'btn-danger',
                            'data-confirm' => '?Translator.trans("role.delete_confirm", [], "RabbleUserBundle")',
                            'data-reload-datatable' => $this->getName(),
                        ]
                    ),
                ],
            ]),
            new GenericDataColumn([
                'expression' => 'data.getName()',
                'searchField' => 'name',
                'sortField' => 'name',
            ]),
        ];
    }
}
