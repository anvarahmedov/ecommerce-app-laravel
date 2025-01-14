import React, {useState} from 'react';
import {Link, router, useForm} from "@inertiajs/react";
import {CartItem as CartItemType, VariationTypeOption} from "@/types";
import TextInput from "@/Components/TextInput";
import CurrencyFormatter from "@/Components/CurrencyFormatter";
import { productRoute } from '@/helpers';

function CartItem({item}: {item: CartItemType}) {
    const [quantity, setQuantity] = useState(item.quantity);
   

    const deleteForm = useForm({
        options_ids: item.option_ids,
        item: item
    })

    const [error, setError] = useState('')

    const onDeleteClick = () => {
        deleteForm.delete(route('cart.destroy', item.product_id), {
            preserveScroll: true,
            data: { item }
        })
    }

    const handleQuantityChange = (ev: React.ChangeEvent<HTMLInputElement>) => {
        console.log(ev.target.value);
        setError('')
        router.put(route('cart.update', item.product_id), {
            quantity: ev.target.value,
            options_ids: item.option_ids
        }, {
            preserveScroll: true,
            onError: (errors) => {
                setError(Object.values(errors)[0])
            }
        });
    };

    return(
        <>
        <div key={item.id} className='flex gap-6 p-3'>
            <Link href={productRoute(item)} className='w-32 min-w-32 min-h-32 flex justify-center self-start'>
                <img src={item.image} alt="" className='max-w-full max-h-full'/>
            </Link>
            <div className='flex-1 flex flex-col'>
                <div className='flex-1'>

                    <h3 className='mb-3 text-sm font-semibold'>
                        <Link href={productRoute(item)}>
                        {item.title}

                        </Link>
                    </h3>

                    <div className='text-xs'>
                        {/*<pre>{JSON.stringify(item, undefined, 2)}</pre>*/}

                        {item.options.map((option: VariationTypeOption) => (
                            <div key={option.id}>
                                <strong className='text-bold'>{option.type.name}:</strong>
                                {option.name}
                            </div>
                        ))}
                    </div>

                </div>
                <div className='flex justify-between items-center mt-4'>
                    <div className='flex gap-2 items-center'>
                        <div className='text-sm'>Quantity:</div>
                        <div className={error ? 'tooltip tooltip-open tooltip-error' : ''} data-tip={error}>
                            <TextInput type="number"
                            defaultValue={item.quantity} onBlur={handleQuantityChange}
                            className="input-sm w-16"></TextInput>
                        </div>
                        <button onClick={(): any => onDeleteClick()} className='btn btn-sm btn-ghost'>
                            Delete
                        </button>
                        <button className='btn btn-sm btn-ghost'>
                            Save For Later
                        </button>
                    </div>
                    <div className='font-bold text-lg'>
                        <CurrencyFormatter amount={item.price * item.quantity}/>
                    </div>
                </div>
            </div>
        </div>

        <div className='divider'>

        </div>
        <script src="{{ asset('js/app.js') }}"></script>

        </>
    );
}

export default CartItem;
