import {Head, Link} from "@inertiajs/react"
import CurrencyFormatter from "@/Components/CurrencyFormatter";
import {CheckCircleIcon} from "@heroicons/react/24/outline";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {PageProps, Order} from "@/types"
function Success({orders}: PageProps<{orders: Order}>) {
    return(
        <AuthenticatedLayout>
            <Head title="Payment was completed"/>
            {/* <pre>{JSON.stringify(order, undefined, 2)}</pre> */ }
            <div className="w-[480px] mx-auto py-8 px-4">
                <div className="text-6xl text-emerald-600 flex justify-center">
                    <CheckCircleIcon className={"size-24"}/>
                </div>
                <div className="text-3xl flex justify-center">
                    Payment was completed
                </div>
            <div className="my-6 text-lg">
                Thanks for your purchase. Your payment completed successfully.
            </div>

            {orders.map((order: Order) => (
                <div key={order.id} className="bg-white dark:bg-gray-800 rounded-lg p-6 mb-4">
                <h3 className="text-3xl mb-3">Order Summary</h3>

                {/* Success Message */}
                <div className="bg-green-100 text-green-800 p-4 rounded-md mb-4">
                    <strong>Success:</strong> Your order was placed successfully!
                </div>

                {/* Seller Information */}
                <div className="flex justify-between mb-2 font-bold">
                    <div className="text-gray-400">Seller</div>
                    <div>
                        <Link href="#" className="hover:underline">
                            {order.vendorUser.store_name}
                        </Link>
                    </div>
                </div>

                {/* Items Information */}
                <div className="flex justify-between mb-3">
                    <div className="text-gray-400">Items</div>
                    <div>{order.orderItems.length}</div>
                </div>

                {/* Total Price */}
                <div className="flex justify-between mb-3">
                    <div className="text-gray-400">Total</div>
                    <div>
                        <CurrencyFormatter amount={order.total_price} />
                    </div>
                </div>

                {/* Action Buttons */}
                <div className="flex justify-between mt-4">
                    <Link href="#" className="btn btn-primary">
                        View Order Details
                    </Link>
                    <Link href={route('dashboard')} className="btn">
                        Back To Home
                    </Link>
                </div>
            </div>


            ))}
</div>
        </AuthenticatedLayout>
    );
}

export default Success;
