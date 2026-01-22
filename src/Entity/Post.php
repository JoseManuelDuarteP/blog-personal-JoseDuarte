<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titulo = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $text = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagen = null;

    #[ORM\Column]
    private ?\DateTime $fechaPublicacion = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    private ?User $user = null;

    /**
     * @var Collection<int, LikePost>
     */
    #[ORM\OneToMany(targetEntity: LikePost::class, mappedBy: 'post')]
    private Collection $likePosts;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'post')]
    private Collection $comments;

    public function __construct()
    {
        $this->fechaPublicacion = new \DateTime();
        $this->likePosts = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): static
    {
        $this->imagen = $imagen;

        return $this;
    }

    public function getFechaPublicacion(): ?\DateTime
    {
        return $this->fechaPublicacion;
    }

    public function setFechaPublicacion(\DateTime $fechaPublicacion): static
    {
        $this->fechaPublicacion = $fechaPublicacion;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, LikePost>
     */
    public function getLikePosts(): Collection
    {
        return $this->likePosts;
    }

    public function countLikes(): int
    {
        return $this->likePosts->count();
    }

    public function addLikePost(LikePost $likePost): static
    {
        if (!$this->likePosts->contains($likePost)) {
            $this->likePosts->add($likePost);
            $likePost->setPost($this);
        }

        return $this;
    }

    public function removeLikePost(LikePost $likePost): static
    {
        if ($this->likePosts->removeElement($likePost)) {
            // set the owning side to null (unless already changed)
            if ($likePost->getPost() === $this) {
                $likePost->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }
}
