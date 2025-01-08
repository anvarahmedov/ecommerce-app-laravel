<?php

namespace App;

enum VendorStatusEnum: string
{
    case Pending = 'pending';

    case Approved = 'approved';

    case Rejected = 'rejected';

}
