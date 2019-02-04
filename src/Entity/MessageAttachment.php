<?php

namespace App\Entity;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageAttachmentRepository")
 */
class MessageAttachment {
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;
	
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $file_name;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Message", inversedBy="attachments")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $message;
	
	/**
	 * @ORM\Column(type="string", length=255, nullable=true)
	 */
	private $path;
	
	private $data;
	
	public function getId(): ?int {
		return $this->id;
	}
	
	public function getFile_Name(): ?string {
		return $this->file_name;
	}
	
	public function setFileName(string $file_name): self {
		$this->file_name = $file_name;
		
		return $this;
	}
	
	public function getData() {
		return $this->data;
	}
	
	public function setData(UploadedFile $data = null): self {
		$this->data = $data;
		
		return $this;
	}
	
	public function getMessage(): ?Message {
		return $this->message;
	}
	
	public function setMessage(?Message $message): self {
		$this->message = $message;
		
		return $this;
	}
	
	public function getPath(): ?string {
		return $this->path;
	}
	
	public function getAbsolutePath() {
		return null === $this->path ? null : $this->getUploadRootDir() . '/' . $this->path;
	}
	
	public function getWebPath() {
		return null === $this->path ? null : $this->getUploadDir() . '/' . $this->path;
	}
	
	protected function getUploadRootDir() {
		// the absolute directory path where uploaded
		// documents should be saved
		return __DIR__ . '/../../../../web/' . $this->getUploadDir();
	}
	
	protected function getUploadDir() {
		// get rid of the __DIR__ so it doesn't screw up
		// when displaying uploaded doc/image in the view.
		return 'uploads/documents';
	}
	
	public function setPath(string $path): self {
		$this->path = $path;
		
		return $this;
	}
	
	public function upload() {
		// the file property can be empty if the field is not required
		if(null === $this->getData()) {
			return;
		}
		
		// use the original file name here but you should
		// sanitize it at least to avoid any security issues
		
		// move takes the target directory and then the
		// target filename to move to
		$this->getData()->move($this->getUploadRootDir(), $this->getData()->getClientOriginalName());
		
		// set the path property to the filename where you've saved the file
		$this->path = $this->getData()->getClientOriginalName();
		
		// clean up the file property as you won't need it anymore
		$this->data = null;
	}
}
