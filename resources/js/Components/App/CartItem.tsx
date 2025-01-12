import React, {useState} from 'react';
import {Link, router, useForm} from "@inertiajs/react";
import {CartItem as CartItemType} from "@/types";
import TextInput from "@/Components/Core/TextInput";
import CurrencyFormatter from "@/Components/Core/CurrencyFormatter";
import { productRoute } from '@/helpers';

function CartItem({item}: {item: CartItemType}) {
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
                        {/*<pre>{JSON.stringify(item, undefined, 2)}*/}
                    </div>
                </div>
            </div>
        </div>

        <div className='divider'>

        </div>
        </>
    );
}

export default CartItem;
