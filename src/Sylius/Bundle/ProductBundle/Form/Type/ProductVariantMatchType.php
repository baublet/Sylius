<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Form\Type;

use Sylius\Bundle\ProductBundle\Form\DataTransformer\ProductVariantToProductOptionsTransformer;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ProductVariantMatchType extends AbstractType
{
    private $availableOptionValues = false;
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ProductInterface $product */
        $product = $options['product'];

        foreach ($product->getOptions() as $i => $option) {
            $this->filterNonExistantProductOptionValues($product, $option);
            $builder->add($option->getCode(), ProductOptionValueChoiceType::class, [
                'label' => $option->getName(),
                'option' => $option,
                'property_path' => '['.$i.']',
            ]);
        }

        $builder->addModelTransformer(new ProductVariantToProductOptionsTransformer($options['product']));
    }

    /**
     * This function filters product options and variants to exclude option values
     * that this product does not have.
     *
     * @param  ProductInterface         $product
     * @param  ProductOptionInterface   $option
     */
    private function filterNonExistantProductOptionValues(ProductInterface $product, ProductOptionInterface $option)
    {
        if($this->availableOptionValues === false) {
            $this->availableOptionValues = [];
            $variants = $product->getVariants();
            foreach($variants as $variant) {
                foreach($variant->getOptionValues() as $optionValue) {
                    $this->availableOptionValues[] = $optionValue->getId();
                }
            }
        }
        foreach($option->getValues() as $optionValue) {
            if(!in_array($optionValue->getId(), $this->availableOptionValues)) {
                $option->removeValue($optionValue);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('product')
            ->setAllowedTypes('product', ProductInterface::class)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_product_variant_match';
    }
}
