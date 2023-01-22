<?php

namespace Tan\SpcSalesRule\Setup\Patch\Data;

use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\BlockRepository;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;

class AddFreeGiftCmsBlock implements DataPatchInterface
{
    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var BlockRepository
     */
    private $blockRepository;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * AddFreeGiftCMSBlock constructor.
     * @param BlockFactory $blockFactory
     * @param BlockRepository $blockRepository
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param LoggerInterface $logger
     */
    public function __construct(
        BlockFactory $blockFactory,
        BlockRepository $blockRepository,
        ModuleDataSetupInterface $moduleDataSetup,
        LoggerInterface $logger
    ) {
        $this->blockFactory = $blockFactory;
        $this->blockRepository = $blockRepository;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->logger = $logger;
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $freeGiftCmsBlockData = [
            'title' => 'SPC Free Gifts',
            'identifier' => 'spc_free_gifts',
            'content' => <<<'BLOCK'
<div data-content-type="row" data-appearance="contained" data-element="main">
   <div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-background-type="image"
   data-video-loop="true" data-video-play-only-visible="true" data-video-lazy-load="true" data-video-fallback-src=""
   data-element="inner"
   style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top;
   background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none;
   border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;">
      <div data-content-type="text" data-appearance="default" data-element="main"
      style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;">
         <h2 id="KCYA4RB" style="text-align: center;"><span style="color: #9e46a0;">Free Gifts</span></h2>
      </div>
   </div>
</div>
BLOCK
            ,
            'is_active' => 1,
            'stores' => Store::DEFAULT_STORE_ID
        ];

        $cmsBlock = $this->blockFactory->create();
        $cmsBlock->setData($freeGiftCmsBlockData);
        try {
            $this->blockRepository->save($cmsBlock);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
