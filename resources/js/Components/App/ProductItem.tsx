import { Product } from '@/types';
import { Link, useForm } from '@inertiajs/react';

import CurrencyFormatter from '../CurrencyFormatter';

function ProductItem({product}: {product: Product}) {
    const form = useForm<{
        options_ids: Record<string, number>;
        quantity: number;
        price: number | null;

       }>({
        options_ids: {},
        quantity: 1,
        price: product.price
       })

    const addToCart = () => {
        form.setData('price', product.price);
        form.post(route('cart.store', product.id), {
            preserveScroll: true,
            preserveState: true,
            onError: (err: any) => {
            console.log(err);
            }
        })
    }

    return (
        <div className='card bg-base-100 shadow-xl'>
            <Link href={route('product.show', product.slug)}>
                <figure>
                    <img src={product.image} alt={product.title}
                    className='aspect-square object-cover'/>
                </figure>
            </Link>
            <div className='card-body'>
                <h2 className='card-title'>{product.title}</h2>
                <p>
                   by <Link href='/' className='hover:underline'>{product.user.name}</Link>&nbsp;
                   in <Link href='/' className='hover:underline'>{product.department.name}</Link>
                </p>
                <div className='card-actions items-center justify-between mt-3'>
                    <button onClick={addToCart} className='btn btn-primary bg-emerald-500 hover:bg-emerald-300 border-0 rounded-xl'>Add to cart</button>
                    <span className='text-2xl'>
                        <CurrencyFormatter amount={product.price}/>
                    </span>
                </div>
            </div>
        </div>
    );
}

export default ProductItem;
