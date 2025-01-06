import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Product, variationType, VariationTypeOption} from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';


import  Carousel  from '@/Components/Carousel'; // Import the Carousel component from the appropriate module
import CurrencyFormatter from '@/Components/CurrencyFormatter';
import { arraysAreEqual } from '@/helpers';

function Show({ product, variationOptions }: { product: Product, variationOptions: number[] }) {
   // console.log(product.slug, variationOptions);

   const form = useForm<{
    options_ids: Record<string, number>;
    quantity: number;
    price: number | null;

   }>({
    options_ids: {},
    quantity: 1,
    price: null
   })

   const {url} = usePage();

   const [selectedOptions, setSelectedOptions] =
   useState<Record<number, VariationTypeOption>>([]);

   const images = useMemo(() => {
    for (let typeID in selectedOptions) {
        const option = selectedOptions[typeID];
        if (option.images.length > 0) return option.images;
    }
    return product.images;
   }, [product, selectedOptions]);

   const computedProduct = useMemo(() => {
    const selectedOptionsIDs = Object.values(selectedOptions).map(op => op.id).sort();

    for (let variation of product.variations) {
        const optionsIDs = variation.variation_type_options_ids.sort();
        if (arraysAreEqual(selectedOptionsIDs, optionsIDs)) {
            return {
                price: variation.price,
                quantity: variation.quantity === null ? Number.MAX_VALUE : variation.quantity
            }
        }
    }
    return {
        price: product.price,
        quantity: product.quantity
    };
   }, [product, selectedOptions])

   useEffect(() => {
    for (let type of product.variationTypes) {
        const selectedOptionID: number = variationOptions[type.id];
        console.log(selectedOptionID, type.options);
        chooseOption(
            type.id,
            type.options.find(op => op.id == selectedOptionID) || type.options[0],
            false
        )
    }
   }, []);

   const getOptionIDsMap = (newOptions:object) => {
    return Object.fromEntries(
        Object.entries(newOptions).map(([a, b]) => [a, b.id])
    )
   }

   const chooseOption = (
    typeID: number,
    option: VariationTypeOption,
    updateRouter: boolean = true
   ) => {
    setSelectedOptions((prevSelectedOptions: any) => {
        const newOptions = {
            ...prevSelectedOptions,
            [typeID]: option
        }
        if (updateRouter) {
            router.get(url, {
                options: getOptionIDsMap(newOptions)
            }, {
                preserveScroll: true,
                preserveState: true
            })
        }
        return newOptions
    })
   }

   const onQuantityChange = (ev: React.ChangeEvent<HTMLSelectElement>) => {
    form.setData('quantity', parseInt(ev.target.value))
   }

   const addToCart = () => {
    form.post(route('cart.store', product.id), {
        preserveScroll: true,
        preserveState: true,
        onError: (err: any) => {
            console.log(err);
        }
    })
   }

   const renderProductVariationTypes = () => {
    return (
        product.variationTypes.map((type: variationType, i: number) => (
            <div key={type.id}>
                <b>{type.name}</b>
                {type.type === 'image' && (
                    <div className='flex gap-2 mb-4'>
                        {type.options.map((option: VariationTypeOption) => (
                            <div onClick={() => chooseOption(type.id, option)} key={option.id}>
                                {option.images && (
                                    <img
                                        src={option.images[0].thumb}
                                        alt=''
                                        className={
                                            'w-[50px]' +
                                            (selectedOptions[type.id]?.id === option.id
                                                ? ' outline outline-4 outline-primary'
                                                : '')
                                        }
                                    />
                                )}
                            </div>
                        ))}
                    </div>
                )}
                {type.type === 'radio' && (
    <div className='flex join mb-4'>
        {type.options.map((option: VariationTypeOption) => (
            <input
                type="radio"
                key={option.id}
                onChange={() => chooseOption(type.id, option)} className='join-item btn'
                value={option.id} checked={selectedOptions[type.id]?.id === option.id}
                name={'variation_type_' + type.id}
                aria-label={option.name}
            />
        ))}
    </div>
)}
            </div>
        ))
    );
};

const renderAddToCartButton = () => {
    return (
        <div className='mb-8 flex gap-4'>
            <select value={form.data.quantity} onChange={onQuantityChange}
            className='select select-bordered w-full'>
                {
                    Array.from({ length: Math.min(10, computedProduct.quantity) }, (_, i) => (
                        <option value={i + 1} key={i + 1}>Quantity: {i + 1}</option>
                    ))
                }
            </select>
            <button onClick={addToCart} className='btn btn-primary'>Add to cart</button>
        </div>
    );
};

   useEffect(() => {
    const idsMap = Object.fromEntries(
        Object.entries(selectedOptions).map(
            ([typeID, option] : [string, VariationTypeOption]) => [typeID, option.id]
        )
    )
    console.log(idsMap)
    form.setData('options_ids', idsMap)
   }, [selectedOptions])



    return (
        <AuthenticatedLayout>
            <Head title={product.slug}/>

            <div className='container mx-auto p-8'>
                <div className='grid gap-8 grid-cols-1 lg:grid-cols-12'>
                    <div className='col-span-7'>
                        <Carousel images={images}/>
                    </div>
                    <div className='col-span-5'>
                        <h1 className='text-2xl mb-8'>{product.title}</h1>
                        <div>
                            <div className='text-3xl font-semibold'>
                                <CurrencyFormatter amount={computedProduct.price}/>
                            </div>
                        </div>
                        {renderProductVariationTypes()}
                        {
                            computedProduct.quantity != undefined && computedProduct.quantity < 10 &&
                            <div className='text-error my-4'>
                                <span>Only {computedProduct.quantity} left</span>
                                </div>
                        }
                        {renderAddToCartButton()}
                        <b className='text-xl'>About the Item</b>
                        <div className='wysiwyg-output' dangerouslySetInnerHTML={{__html: product.description}}/>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}


export default Show;
