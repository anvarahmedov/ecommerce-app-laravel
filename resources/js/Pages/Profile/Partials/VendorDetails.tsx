import PrimaryButton from '@/Components/PrimaryButton';
import {useForm, usePage} from '@inertiajs/react';
import React, {FormEventHandler, useState} from 'react';
import SecondaryButton from '@/Components/SecondaryButton';
import Modal from '@/Components/Modal';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';
import { Textarea } from '@headlessui/react';

export default function VendorDetails(
    props: {
        className?: string;
    },
) {
    const [showBecomeVendorConfirmation, setShowBecomeVendorConfirmation] = useState(false);
    const [recentlySuccessful, setRecentlySuccessful] = useState(false);
    const [successMessage, setSuccessMessage] = useState('');

    const user = usePage().props.auth.user;
    const token = usePage().props.csrf_token;
    const {
        data,
        setData,
        post,
        processing,
        errors,

    } =
        useForm({
            store_name: user.vendor?.store_name || user.name.toLowerCase()
    .replace(/[^a-z0-9\s\-@.]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .replace(/^-|-$/g, ''),
            store_address: user.vendor?.store_address,
        });

        const onStoreChangeName = (e: React.ChangeEvent<HTMLInputElement>) => {
            setData('store_name', e.target.value.toLowerCase().replace(/\s+/g, '-'));
        }



        const becomeVendor:FormEventHandler = (ev: React.FormEvent<HTMLFormElement>) => {
            ev.preventDefault();

            post(route('vendor.store'), {
                preserveScroll: true,
                onSuccess: () => {
                    closeModal();
                    setSuccessMessage('You can now create and publish products');

                    setRecentlySuccessful(true);

                    setTimeout(() => {
                        setRecentlySuccessful(false);
                    }, 3000);
                },
                onError: () => {
                    setSuccessMessage('Something went wrong');
                },
            })
        }

        const updateVendor:FormEventHandler = (ev: React.FormEvent<HTMLFormElement>) => {
            ev.preventDefault();

            post(route('vendor.update'), {
                preserveScroll: true,
                onSuccess: () => {
                    closeModal();

                    setSuccessMessage("Store details updated successfully");

                    setRecentlySuccessful(true);

                    setTimeout(() => {
                        setRecentlySuccessful(false);
                    }, 3000);

                },
                onError: () => {
                    setSuccessMessage('Something went wrong');
                },

            })
        }

        const closeModal = () => {
            setShowBecomeVendorConfirmation(false);
        }

        const onStoreNameChange = (e: React.ChangeEvent<HTMLInputElement>) => {
            setData('store_name', e.target.value.toLowerCase().replace(/\s+/g, '-'));
        }



    return (
        <section className={props.className}>
            {recentlySuccessful && <div className='toast toast-top toast-end bg-green-500 text-gray-800 p-4 sm:rounded-xl mt-14 mr-5'>
            <div>
                    <span>{successMessage}</span>
            </div>
            </div>}

            <header>
                <h2 className="flex justify-between mb-8 text-lg font-medium text-gray-900 dark:text-gray-100">
                    Vendor Details
                    {user.vendor?.status === 'pending' && <span className="badge badge-warning">{user.vendor.status_label}</span>}
                    {user.vendor?.status === 'approved' && <span className="badge badge-success">{user.vendor.status_label}</span>}
                    {user.vendor?.status === 'rejected' && <span className="badge badge-error">{user.vendor.status_label}</span>}
                </h2>


            </header>

            <div>
                {!user.vendor && <PrimaryButton onClick={() => setShowBecomeVendorConfirmation(true)} disabled={processing}>
                    Become a vendor
            </PrimaryButton>
                }

                {user.vendor && (
                    <>
                    <form onSubmit={updateVendor}>
                        <div className='mb-4'>
                            <InputLabel htmlFor="name" value="Store Name" />

                            <TextInput
                                id="name"
                                className='mt-1 block w-full'
                                value={data.store_name}
                                onChange={onStoreNameChange}
                                required
                                isFocused={true}
                                autoComplete="name"
                            />
                            <InputError message={errors.store_name} className='mt-2'/>
                        </div>

                        <div className='mb-4'>
                            <InputLabel htmlFor="address" value="Store Address"/>

                            <TextInput
                                className="mt-1 block w-full"
                                value={data.store_address}
                                onChange={(e) => setData('store_address', e.target.value) }
                                placeholder="Enter your store address"
                                >
                            </TextInput>
                            <InputError message={errors.store_address} className='mt-2'/>
                        </div>
                        <div className='flex items-center gap-4 py-4'>
                            <PrimaryButton disabled={processing}>
                                Update
                            </PrimaryButton>
                        </div>
                    </form>

                    <form action={route('stripe.connect')}
                    method={'post'}
                    className='{my-8}'>
                        <InputLabel htmlFor="stripe_account_active"/>
                        <input type = "hidden" name="_token" value={token}/>
                        {user.stripe_account_active && (<div className={'text-center text-gray-600 my-4 text-sm'}>
                            You are successfully connected to Stripe.
                        </div>)}

                        <button className='btn btn-primary w-full' disabled={user.stripe_account_active}>
                            Connect to Stripe
                        </button>
                    </form>
                    </>
                )}

            </div>
            <Modal show={showBecomeVendorConfirmation} onClose={closeModal}>
                <form onSubmit={becomeVendor} className='p-8'>
                    <h2 className='text-lg font-medium text-gray-900 dark:text-gray-100'>Are you sure to become a vendor?</h2>

                    <div className = "mt-6 flex justify-end">
                        <SecondaryButton
                            onClick={closeModal}
                        >
                            Cancel
                        </SecondaryButton>

                        <PrimaryButton className='ms-3 ml-4' disabled={processing}>
                            Confirm
                        </PrimaryButton>
                    </div>
                </form>
            </Modal>

            </section>
    );
}