<?php

namespace Laravel\Spark\Repositories;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Laravel\Spark\Token;
use Laravel\Spark\Contracts\Repositories\TokenRepository as Contract;

class TokenRepository implements Contract
{
    /**
     * {@inheritdoc}
     */
    public function validToken($token)
    {
        return Token::where('token', $token)->where(function ($query) {
            return $query->whereNull('expires_at')
                         ->orWhere('expires_at', '>=', Carbon::now());
        })->first();
    }

    /**
     * {@inheritdoc}
     */
    public function createTransientToken($user, array $data = [])
    {
        return $this->createToken(
            $user, 'Web Token', array_merge($data, ['xsrf' => csrf_token()]), true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createToken($user, $name, array $data = [], $transient = false)
    {
        $this->deleteExpiredTokens($user);

        $expiration = $transient ? Carbon::now()->addMinutes(5) : null;

        return $user->tokens()->create([
            'id' => Uuid::uuid4(),
            'user_id' => $user->id,
            'name' => $name,
            'token' => str_random(60),
            'metadata' => $data,
            'transient' => $transient,
            'expires_at' => $transient ? $expiration : null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function createEncryptedTokenCookie($user)
    {
        return $this->createTokenCookie($user, true);
    }

    /**
     * {@inheritdoc}
     */
    public function createTokenCookie($user, $encrypt = false)
    {
        $token = $this->createTransientToken($user)->token;

        return cookie(
            'spark_token', $encrypt ? encrypt($token) : $token, 5, null,
            config('session.domain'), config('session.secure'), true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function updateToken(Token $token, $name, array $abilities = [])
    {
        $metadata = $token->metadata;

        $metadata['abilities'] = $abilities;

        $token->forceFill([
            'name' => $name,
            'metadata' => $metadata,
        ])->save();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteExpiredTokens($user)
    {
        $user->tokens()->where('expires_at', '<=', Carbon::now())->delete();
    }
}
