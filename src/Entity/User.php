<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
// #[UniqueEntity('email', message: "L'email est déjà utiliser. Avez-vous oublier votre mot de passe ?")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column]
    #[Groups(['user'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
    #[Groups(['user'])]

    private ?string $email = null;

    #[ORM\Column(length: 50)]
    #[Assert\Choice(choices: ['ROLE_USER', 'ROLE_ADMIN'], multiple: true, message: 'Rôle invalide')]
    #[Groups(['user'])]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    // #[Assert\Regex('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/')]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    #[Groups(['user'])]
    #[Assert\NotBlank(message: 'Vous devez renseigner votre prénom')]
    private ?string $firstname = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Vous devez renseigner votre nom')]
    private ?string $lastname = null;

    #[ORM\Column(length: 5)]
    #[Assert\Choice(choices: ['homme', 'femme'], message: 'Genre invalide')]
    #[Groups(['user'])]
    #[Assert\NotBlank(message: 'Vous devez choisir un genre')]
    private ?string $gender = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user'])]
    private ?int $size = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user'])]
    private ?int $weight = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user'])]
    private ?int $caloric_need = null;

    #[ORM\Column(nullable: true, type: Types::DATE_MUTABLE)]
    #[Groups(['user'])]
    private ?\DateTimeInterface $date_of_birth = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Hydration::class, orphanRemoval: true)]
    #[Groups(['user'])]
    private Collection $hydrations;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Sleep::class, orphanRemoval: true)]
    #[Groups(['user'])]
    private Collection $sleeps;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Activity::class, orphanRemoval: true)]
    #[Groups(['user'])]
    private Collection $activities;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Food::class, orphanRemoval: true)]
    #[Groups(['user'])]
    private Collection $foods;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Drug::class, orphanRemoval: true)]
    #[Groups(['user'])]
    private Collection $drugs;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Smoke::class, orphanRemoval: true)]
    private Collection $smokes;

    public function __construct()
    {
        $this->setCreatedAt(new DateTimeImmutable());
        $this->activities = new ArrayCollection();
        $this->drugs = new ArrayCollection();
        $this->foods = new ArrayCollection();
        $this->hydrations = new ArrayCollection();
        $this->sleeps = new ArrayCollection();
        $this->smokes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // Do nothing
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->date_of_birth;
    }

    public function setDateOfBirth(?\DateTimeInterface $date_of_birth): self
    {
        $this->date_of_birth = $date_of_birth;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection<int, Hydration>
     */
    public function getHydrations(): Collection
    {
        return $this->hydrations;
    }

    public function addHydration(Hydration $hydration): self
    {
        if (!$this->hydrations->contains($hydration)) {
            $this->hydrations->add($hydration);
            $hydration->setUserId($this);
        }

        return $this;
    }

    public function removeHydration(Hydration $hydration): self
    {
        if ($this->hydrations->removeElement($hydration)) {
            // set the owning side to null (unless already changed)
            if ($hydration->getUserId() === $this) {
                $hydration->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sleep>
     */
    public function getSleeps(): Collection
    {
        return $this->sleeps;
    }

    public function addSleep(Sleep $sleep): self
    {
        if (!$this->sleeps->contains($sleep)) {
            $this->sleeps->add($sleep);
            $sleep->setUserId($this);
        }

        return $this;
    }

    public function removeSleep(Sleep $sleep): self
    {
        if ($this->sleeps->removeElement($sleep)) {
            // set the owning side to null (unless already changed)
            if ($sleep->getUserId() === $this) {
                $sleep->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities->add($activity);
            $activity->setUserId($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getUserId() === $this) {
                $activity->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Food>
     */
    public function getFoods(): Collection
    {
        return $this->foods;
    }

    public function addFood(Food $food): self
    {
        if (!$this->foods->contains($food)) {
            $this->foods->add($food);
            $food->setUserId($this);
        }

        return $this;
    }

    public function removeFood(Food $food): self
    {
        if ($this->foods->removeElement($food)) {
            // set the owning side to null (unless already changed)
            if ($food->getUserId() === $this) {
                $food->setUserId(null);
            }
        }

        return $this;
    }

    public function getCaloricNeed(): ?int
    {
        return $this->caloric_need;
    }

    public function setCaloricNeed(?int $caloric_need): self
    {
        $this->caloric_need = $caloric_need;

        return $this;
    }

    /**
     * @return Collection<int, Drug>
     */
    public function getDrugs(): Collection
    {
        return $this->drugs;
    }

    public function addDrug(Drug $drug): self
    {
        if (!$this->drugs->contains($drug)) {
            $this->drugs->add($drug);
            $drug->setUserId($this);
        }

        return $this;
    }

    public function removeDrug(Drug $drug): self
    {
        if ($this->drugs->removeElement($drug)) {
            // set the owning side to null (unless already changed)
            if ($drug->getUserId() === $this) {
                $drug->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Smoke>
     */
    public function getSmokes(): Collection
    {
        return $this->smokes;
    }

    public function addSmoke(Smoke $smoke): self
    {
        if (!$this->smokes->contains($smoke)) {
            $this->smokes->add($smoke);
            $smoke->setUser($this);
        }

        return $this;
    }

    public function removeSmoke(Smoke $smoke): self
    {
        if ($this->smokes->removeElement($smoke)) {
            // set the owning side to null (unless already changed)
            if ($smoke->getUser() === $this) {
                $smoke->setUser(null);
            }
        }

        return $this;
    }
}
