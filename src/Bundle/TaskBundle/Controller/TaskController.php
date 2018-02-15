<?php

namespace DMKProjectM\Bundle\TaskBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use Oro\Bundle\UserBundle\Entity\User;
use DMKProjectM\Bundle\TaskBundle\Entity\Task;
use DMKProjectM\Bundle\TaskBundle\Form\Type\TaskType;

/**
 * @Route("/task")
 */
class TaskController extends Controller
{
    /**
     * @Route(
     *      ".{_format}",
     *      name="projectm_task_index",
     *      requirements={"_format"="html|json"},
     *      defaults={"_format" = "html"}
     * )
     * @Acl(
     *      id="projectm_task_view",
     *      type="entity",
     *      class="DMKProjectMTaskBundle:Task",
     *      permission="VIEW"
     * )
     * @Template
     */
    public function indexAction()
    {
        return [
            'entity_class' => $this->container->getParameter('projectm_task.entity.class')
        ];
    }

    /**
     * @Route("/create", name="projectm_task_create")
     * @Acl(
     *      id="projectm_task_create",
     *      type="entity",
     *      class="DMKProjectMTaskBundle:Task",
     *      permission="CREATE"
     * )
     * @Template("DMKProjectMTaskBundle:Task:update.html.twig")
     */
    public function createAction()
    {
        $task = new Task();

        $defaultPriority = $this->getRepository('DMKProjectMTaskBundle:TaskPriority')->find('normal');
        if ($defaultPriority) {
            $task->setTaskPriority($defaultPriority);
        }

        $formAction = $this->get('oro_entity.routing_helper')
            ->generateUrlByRequest('projectm_task_create', $this->getRequest());

        return $this->update($task, $formAction);
    }

    /**
     * @return User
     */
    protected function getCurrentUser()
    {
        $token = $this->container->get('security.context')->getToken();

        return $token ? $token->getUser() : null;
    }

    /**
     * @Route("/view/{id}", name="projectm_task_view", requirements={"id"="\d+"})
     * @AclAncestor("projectm_task_view")
     * @Template
     */
    public function viewAction(Task $task)
    {
        //TODO: get TaskTree
        $jsTaskTree = [];
        //$jsTaskTree = $this->get('task tree provider')->createTree($root, true);

        return [
            'jsTree' => $jsTaskTree,
            'entity' => $task
        ];
    }

    /**
     * This action is used to render the list of tasks associated with the given entity
     * on the view page of this entity
     *
     * @Route(
     *      "/activity/view/{entityClass}/{entityId}",
     *      name="projectm_task_activity_view"
     * )
     *
     * @AclAncestor("projectm_task_view")
     * @Template
     */
    public function activityAction($entityClass, $entityId)
    {
        return array(
            'entity' => $this->get('oro_entity.routing_helper')->getEntity($entityClass, $entityId)
        );
    }

    /**
     * @Route("/update/{id}", name="projectm_task_update", requirements={"id"="\d+"})
     * @Template
     * @Acl(
     *      id="projectm_task_update",
     *      type="entity",
     *      class="DMKProjectMTaskBundle:Task",
     *      permission="EDIT"
     * )
     */
    public function updateAction(Task $task)
    {
        $formAction = $this->get('router')->generate('projectm_task_update', ['id' => $task->getId()]);

        return $this->update($task, $formAction);
    }

    /**
     * @Route("/widget/info/{id}", name="projectm_task_widget_info", requirements={"id"="\d+"})
     * @Template
     * @AclAncestor("projectm_task_view")
     */
    public function infoAction(Task $entity)
    {
        return [
            'entity'         => $entity,
            'target'         => $this->getTargetEntity(),
            'renderContexts' => true
        ];
    }

    /**
     * @param Task $task
     * @param string $formAction
     * @return array
     */
    protected function update(Task $task, $formAction)
    {
        $saved = false;
        if ($this->get('projectm_task.form.handler.task')->process($task)) {
            if (!$this->getRequest()->get('_widgetContainer')) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    $this->get('translator')->trans('projectm.task.saved_message')
                );

                return $this->get('oro_ui.router')->redirect($task);
            }
            $saved = true;
        }

        return array(
            'entity'     => $task,
            'saved'      => $saved,
            'form'       => $this->get('projectm_task.form.handler.task')->getForm()->createView(),
            'formAction' => $formAction,
        );
    }

    /**
     * @return TaskType
     */
    protected function getFormType()
    {
        return $this->get('projectm_task.form.handler.task')->getForm();
    }

    /**
     * @param string $entityName
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository($entityName)
    {
        return $this->getDoctrine()->getRepository($entityName);
    }

    /**
     * Get target entity
     *
     * @return object|null
     */
    protected function getTargetEntity()
    {
        $entityRoutingHelper = $this->get('oro_entity.routing_helper');
        $targetEntityClass   = $entityRoutingHelper->getEntityClassName($this->getRequest(), 'targetActivityClass');
        $targetEntityId      = $entityRoutingHelper->getEntityId($this->getRequest(), 'targetActivityId');
        if (!$targetEntityClass || !$targetEntityId) {
            return null;
        }

        return $entityRoutingHelper->getEntity($targetEntityClass, $targetEntityId);
    }
}
