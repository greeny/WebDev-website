<?php
/**
 * @author Tomáš Blatný
 */

namespace WebDev\Model;

/**
 * @property int $id
 * @property User $user m:hasOne
 * @property string $title
 * @property string $text
 * @property int $time
 */
class Post extends BaseEntity {

}
 