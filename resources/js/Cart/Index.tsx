import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { PageProps } from "@/types";
import { Head } from "@inertiajs/react";
function Index(  {
    csrf_token,
    cartItems,
    totalQuantity,
    totalPrice
}: PageProps<{cartItems: Record<number, GroupedCartItems>}>) {
    return (
        <AuthenticatedLayout>
            <Head title="Your Cart"/>

            <div className="container mx-auto p-8 flex flex-col lg:flex-row gap-4">
                <div className="card flex-1 bg-white dark:bg-gray-800 order-2 lg:order-1">
                    <div className="card-body">

                    </div>
                </div>
                <div className="card bg-white dark:bg-gray-800 lg:min-w-[260px] order-1 lg:order-2">
                    <div className="card-body">

                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

export default Index;
