import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Product} from '@/types';
import { Head } from '@inertiajs/react';

import { Helmet } from 'react-helmet';

function Show({ product, variationOptions }: { product: Product, variationOptions: number[] }) {
    console.log(product.slug, variationOptions);
    return (
        <AuthenticatedLayout>
            <Head title={product.slug}/>
        </AuthenticatedLayout>
    );
}


export default Show;
