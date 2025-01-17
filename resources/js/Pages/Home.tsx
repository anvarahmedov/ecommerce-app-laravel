import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { PageProps, PaginationProps, Product } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';
import ProductItem from '@/Components/App/ProductItem';
import GuestLayout from '@/Layouts/GuestLayout';

export default function Home({
    products
}: PageProps<{ products: PaginationProps<Product> }>) {
   // const user = usePage().props.auth?.user;

  //  if (!user) {
    //    return (
   //
   //     );
  //  }
    return (
        <AuthenticatedLayout>
            <Head title="Welcome" />
            <div className="hero bg-gray-750 h-[300px]">

              <div className="hero-content text-center font-huawei">
    <div className="max-w-md mt-12 sm:mt-16 md:mt-20 lg:mt-24">
      <h1 className="text-5xl font-bold">Hello there</h1>
      <p className="py-6">
        Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem
        quasi. In deleniti eaque aut repudiandae et a id nisi.
      </p>
      <button className="btn btn-primary bg-emerald-500 hover:bg-emerald-300 border-0 rounded-xl">Get Started</button>
    </div>
  </div>
</div>
<div className='grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3 p-8 mt-12 sm:mt-16 md:mt-20 lg:mt-24'>
        {
            products.data.map((product: Product) => (
                <ProductItem product={product} key={product.id}/>
            ))
        }
</div>
        </AuthenticatedLayout>
    );
}
