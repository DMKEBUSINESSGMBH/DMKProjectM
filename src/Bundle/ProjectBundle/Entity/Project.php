<?php
namespace DMKProjectM\Bundle\ProjectBundle\Entity;

use DMKProjectM\Bundle\ProjectBundle\Model\ExtendProject;
use Doctrine\ORM\Mapping as ORM;
use EHDev\BasicsBundle\Entity\Traits\BUOwnerTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;

/**
 * @ORM\Entity(repositoryClass="DMKProjectM\Bundle\ProjectBundle\Entity\Repository\ProjectRepository")
 * @ORM\Table(name="projectm_project")
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *      routeName="projectm_project_index",
 *      routeView="projectm_project_view",
 *      defaultValues={
 *          "dataaudit"={
 *              "auditable"=true
 *          },
 *          "entity"={
 *              "icon"="fa-list-alt"
 *          },
 *          "ownership"={
 *              "owner_type"="BUSINESS_UNIT",
 *              "owner_field_name"="owner",
 *              "owner_column_name"="business_unit_owner_id",
 *              "organization_field_name"="organization",
 *              "organization_column_name"="organization_id"
 *          },
 *          "security"={
 *              "type"="ACL",
 *              "category"="projectm_management"
 *          },
 *          "grid"={
 *              "default"="projectm-project-project-grid"
 *          },
 *          "form"={
 *              "grid_name"="projectm-project-project-grid",
 *              "form_type"="projectm_project_select"
 *          },
 *          "tag"={"enabled"=true}
 *      }
 * )
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Project extends ExtendProject
{
    use BUOwnerTrait;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $name = '';

    /**
     * @var string|null
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var bool
     * @ORM\Column(type="boolean", name="is_active")
     */
    protected $isActive = false;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Project
     */
    public function setName(string $name): Project
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     * @return Project
     */
    public function setDescription(string $description = null): Project
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     * @return Project
     */
    public function setIsActive(bool $isActive): Project
    {
        $this->isActive = $isActive;
        return $this;
    }
}
