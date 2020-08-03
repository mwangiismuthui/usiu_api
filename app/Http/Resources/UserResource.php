<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            // 'id'=>$this->id,
            'full_name'=>$this->full_name,
            'student_id'=>$this->student_id,
            'phone_number'=>$this->phone_number,
            'profile_picture'=>$this->profile_picture,
            'status'=>$this->status,
            'is_partner'=>$this->is_partner,
            'email'=>$this->email,
            'created_at'=>$this->created_at->format('d M, yy'),
            'token' =>  $this->createToken('token')->accessToken,
          ];
    }
}
