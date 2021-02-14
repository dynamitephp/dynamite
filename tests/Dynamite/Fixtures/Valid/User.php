<?php
declare(strict_types=1);

namespace Dynamite\Fixtures\Valid;
use Dynamite\Configuration as Dynamite;


/**
 * @Dynamite\Item(objectType="USER")
 * @Dynamite\PartitionKeyFormat("USER#{id}")
 * @Dynamite\SortKeyFormat("USER")
 * @Dynamite\DuplicateTo(pk="UDATA#{email}", sk="UDATA", props={"id", "email", "username"})
 * @Dynamite\DuplicateTo(pk="UDATA#{username}", sk="UDATA", props={"id", "email", "username"})
 */
class User
{

    /**
     * @Dynamite\PartitionKey()
     * @var string
     */
    protected string $pk;

    /**
     * @Dynamite\SortKey()
     * @var string
     */
    protected string $sk;

    /**
     * @Dynamite\Attribute(type="string", name="id")
     * @var string
     */
    private string $id;

    /**
     * @Dynamite\Attribute(type="string", name="mail")
     * @var string
     */
    private string $email;

    /**
     * @Dynamite\Attribute(type="string", name="nick")
     * @var string
     */
    private string $username;

    /**
     * @Dynamite\Attribute(type="string", name="dnam")
     * @var string
     */
    private string $usersDogName;


    public function __construct(string $id, string $email, string $username, string $usersDogName)
    {
        $this->id = $id;
        $this->email = $email;
        $this->username = $username;
        $this->usersDogName = $usersDogName;
    }
}