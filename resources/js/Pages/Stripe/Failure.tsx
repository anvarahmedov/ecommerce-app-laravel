import { Head, Link } from "@inertiajs/react";
import { XCircleIcon } from "@heroicons/react/24/outline";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { PageProps, Order } from "@/types";

function Failure({ orders }: PageProps<{ orders: Order }>) {
    return (
        <AuthenticatedLayout>
            <Head title="Payment Failed" />
            <div className="w-[480px] mx-auto py-8 px-4">
                <div className="text-6xl text-red-600 flex justify-center">
                    <XCircleIcon className="size-24" />
                </div>
                <div className="text-3xl flex justify-center text-red-600">
                    Payment Failed
                </div>
                <div className="my-6 text-lg text-gray-700 dark:text-gray-300">
                    Unfortunately, your payment was not successful. Please try again or contact support.
                </div>
                {orders.map((order: Order) => (
                    <div key={order.id} className="bg-white dark:bg-gray-800 rounded-lg p-6 mb-4">
                        <h3 className="text-3xl mb-3">Order Summary</h3>

                        {/* Failure Message */}
                        <div className="bg-red-100 text-red-800 p-4 rounded-md mb-4">
                            <strong>Error:</strong> Your order could not be processed.
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
                            <div className="font-semibold">${order.total_price}</div>
                        </div>

                        {/* Action Buttons */}
                        <div className="flex justify-between mt-4">
                            <Link href="#" className="btn btn-primary">
                                Retry Payment
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

export default Failure;