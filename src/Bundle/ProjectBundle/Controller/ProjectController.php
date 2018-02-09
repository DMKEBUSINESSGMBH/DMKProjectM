<?php
namespace DMKProjectM\Bundle\ProjectBundle\Controller;

use DMKProjectM\Bundle\ProjectBundle\Entity\Project;
use Oro\Bundle\SoapBundle\Entity\Manager\ApiEntityManager;

use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\SecurityBundle\Annotation\Acl;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/project")
 */
class ProjectController extends Controller
{
    /**
     * Index
     * @Route("/", name="projectm_project_project_index")
     * @AclAncestor("projectm_project_project_view")
     *
     * @Template
     */
    public function indexAction(): array
    {
        return [];
    }

    /**
     * @Route("/view/{id}", name="projectm_project_project_view", requirements={"id"="\d+"})
     * @Template
     * @Acl(
     *      id="projectm_project_project_view",
     *      type="entity",
     *      permission="VIEW",
     *      class="DMKProjectMProjectBundle:Project"
     * )
     */
    public function viewAction(Project $project): array
    {
        return [
            'entity' => $project,
        ];
    }

    /**
     * @Route("/create", name="projectm_project_project_create")
     * @Template("@DMKProjectMProject/Project/widget/update.html.twig")
     * @Acl(
     *      id="projectm_project_project_create",
     *      type="entity",
     *      permission="CREATE",
     *      class="DMKProjectMProjectBundle:Project"
     * )
     */
    public function createAction(): array
    {
        return $this->update($this->getManager()->createEntity());
    }

    /**
     * @Route("/update/{id}", name="projectm_project_project_update", requirements={"id"="\d+"})
     *
     * @Template
     * @Acl(
     *      id="projectm_project_project_update",
     *      type="entity",
     *      permission="EDIT",
     *      class="DMKProjectMProjectBundle:Project"
     * )
     */
    public function updateAction(Project $project)
    {
        return $this->update($project);
    }

    private function update(Project $project)
    {
        return $this->get('oro_form.update_handler')->update(
            $customer,
            $this->get(''),
//            $this->get('translator')->trans('')
            'SAVED'
        );
    }

    protected function getManager(): ApiEntityManager
    {
        return $this->get('projectm.project.project.manager');
    }
}
