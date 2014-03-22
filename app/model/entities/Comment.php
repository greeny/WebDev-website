<?php
/**
 * @author Tomáš Blatný
 */

namespace WebDev\Model;

/**
 * @property int $id
 * @property User|NULL $user m:hasOne
 * @property string $text
 * @property int $time
 */
class Comment extends BaseEntity {

}
 