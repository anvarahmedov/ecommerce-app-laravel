
import { Link, usePage } from '@inertiajs/react';
import React from 'react'
import CurrencyFormatter from '../CurrencyFormatter';
import MiniCartDropdown from '../MiniCartDropdown';


function Navbar() {
    const {auth, totalPrice, totalQuantity} = usePage().props;
    const {user} = auth;
    return(
        <div className="navbar bg-base-100">
  <div className="flex-1">
    <a className="btn btn-ghost text-xl text-purpure" href='/'>Gemini</a>
  </div>
  <div className="flex-none gap-4">
    <MiniCartDropdown/>
    
    <div className="dropdown dropdown-end ms-4">
      <div tabIndex={0} role="button" className="btn btn-ghost btn-circle avatar me-1 sm:me-2 md:me-3 lg:me-4 xl:me-6">
        <div className="w-10 rounded-full">
          <img
            alt="Tailwind CSS Navbar component"
            src="https://img.daisyui.com/images/stock/photo-1534528741775-53994a69daeb.webp" />
        </div>
      </div>
      <ul
        tabIndex={0}
        className="menu menu-sm dropdown-content bg-base-100 rounded-box z-[1] mt-3 w-52 p-2 shadow">
        <li>
          <Link href={route('profile.edit')} className="justify-between hover:text-purpure">
            Profile
          </Link>
        </li>
        <li><Link href={route('logout')} method={"post"} as="button" className='hover:text-purpure'>Logout</Link></li>
      </ul>
    </div>

  </div>
</div>
    );
}

export default Navbar;

