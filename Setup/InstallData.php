<?php
/**
 * Copyright © 2016 Ihor Vansach (ihor@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\BlogSampleData\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magefan\Blog\Model\CategoryFactory;
use Magefan\Blog\Model\PostFactory;
use Magefan\Blog\Model\AuthorFactory;

class InstallData implements InstallDataInterface
{
    /**
     * @var \Magefan\Blog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Magefan\Blog\Model\PostFactory
     */
    protected $_postFactory;

    /**
     * @var \Magefan\Blog\Model\AuthorFactory
     */
    protected $_authorFactory;


    /**
     * @param \Setup\SampleData\Executor $executor
     * @param \Magefan\Blog\Model\CategoryFactory $categoryFactory
     * @param \Magefan\Blog\Model\AuthorFactory $authorFactory
     */
    public function __construct(
        CategoryFactory $categoryFactory,
        PostFactory $postFactory,
        AuthorFactory $authorFactory
    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->_postFactory = $postFactory;
        $this->_authorFactory = $authorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /* Create categories */
        $categoryData = [
            'title' => 'Category {n}',
            'is_active' => 1,
            'store_ids' => [0],
            'path' => 0,
        ];
        $categoriesId = [];

        for ($i = 1; $i < 4; $i++) {
            $data = $categoryData;
            $data['title'] = str_replace('{n}', $i, $data['title']);
            $category = $this->_categoryFactory->create()->setData($data)->save();
            $categoriesId[] = $parentCategoryId = $category->getId();
            for ($j = 1; $j < 3; $j++) {
                $data = $categoryData;
                $data['title'] = str_replace('{n}', $i . '.' . $j, $data['title']);
                $data['path'] = $parentCategoryId;
                $category = $this->_categoryFactory->create()->setData($data)->save();
                $categoriesId[] = $parentCategoryId = $category->getId();
            }

        }

        /* Create Posts */
        $postData = [
            'title' => 'Article {n}',
            'is_active' => 1,
            'store_ids' => [0],
            'categories' => [],
            'relatedposts_links' => [],
            'author_id' => 0,
        ];
        $postsId = [];
        $authorsId = $this->_authorFactory->create()->getCollection()->getAllIds();
        
        $time = time() - 86400 * 200;
        for ($i = 1; $i < 23; $i++) {
            $data = $postData;
            $data['title'] = str_replace('{n}', $i, $data['title']);
            $data['content'] = str_replace('{n}', $i, $this->_getPostContent());
            $data['publish_time'] = $time + 86400 * $i * 3;
            $data['creation_time'] = $data['publish_time'];
            $data['update_time'] =  $data['publish_time'];

            if (count($categoriesId)) {
                for ($x = 0; $x < 2; $x++) {
                    $cid = $categoriesId[rand(0,  count($categoriesId) - 1)];
                    $data['categories'][$cid] = $cid;
                }
            }

            if (count($postsId)) {
                for ($x = 0; $x < 5; $x++) {
                    $pid = $postsId[rand(0, count($postsId) - 1)];
                    $data['relatedposts_links'][$pid] = $pid;
                }
            }

            if (count($authorsId)) {
                $data['author_id'] = $authorsId[rand(0, count($authorsId) - 1)];
            }

            $post = $this->_postFactory->create()->setData($data)->save();
            $postsId[] = $post->getId();

        }

    }

    protected function _getPostContent()
    {
        $images = [
            'https://upload.wikimedia.org/wikipedia/commons/c/c5/Single_Color_Flag_-_DCD0FF.svg',
            'https://upload.wikimedia.org/wikipedia/commons/3/31/Single_Color_Flag_-_FFFF00.svg',
            'https://upload.wikimedia.org/wikipedia/commons/6/60/Single_Color_Flag_-_0087DC.png',
            'https://upload.wikimedia.org/wikipedia/commons/3/33/Single_Color_Flag_-_E65C01.svg',
            'https://upload.wikimedia.org/wikipedia/commons/e/e4/Single_Color_Flag_-_F6002F.svg',
            'https://upload.wikimedia.org/wikipedia/commons/7/78/Single_Color_Flag_-_007500.svg',
            'https://upload.wikimedia.org/wikipedia/commons/1/1e/Single_Color_Flag_-_990066.svg',
        ];

        return '
                <p>
                <img src="' . $images[rand(0, count($images) - 1)] . '" style="width:25%; float:left;" />
                The tag &lt;p&gt; defines a paragraph. Browsers automatically add some space (margin) before and after each &lt;p&gt; element. The margins can be modified with CSS (with the margin properties). The tag &lt;p&gt; defines a paragraph. Browsers automatically add some space (margin) before and after each &lt;p&gt; element. The margins can be modified with CSS (with the margin properties). The tag &lt;p&gt; defines a paragraph. Browsers automatically add some space (margin) before and after each &lt;p&gt; element. The margins can be modified with CSS (with the margin properties).The tag &lt;p&gt; defines a paragraph. Browsers automatically add some space (margin) before and after each &lt;p&gt; element. The margins can be modified with CSS (with the margin properties). The tag &lt;p&gt; defines a paragraph. Browsers automatically add some space (margin) before and after each &lt;p&gt; element. The margins can be modified with CSS (with the margin properties). The tag &lt;p&gt; defines a paragraph. Browsers automatically add some space (margin) before and after each &lt;p&gt; element. The margins can be modified with CSS (with the margin properties).
                </p>
                <!-- pagebreak -->
                <h2>Header Level 2</h2>
                <ol>
                <li>Coffee</li>
                <li>Tea</li>
                <li>Milk</li>
                </ol>
                <h3>Header Level 3</h3>
                <ul>
                <li>Coffee</li>
                <li>Tea</li>
                <li>Milk</li>
                </ul>
                <h4>Header Level 4</h4>
                <p>Please read&nbsp;<a title="Magento 2 Blog online documentation" href="http://magefan.com/docs/magento-2-blog/" target="_blank">Online documentation</a>&nbsp;and&nbsp;<a href="http://magefan.com/blog/add-read-more-tag-to-blog-post-content/" target="_blank">How to add "read more" tag to post content</a></p>
                ';
    }
}

