<?php
/**
 * Created by PhpStorm.
 * User: zzg
 * Date: 2020-07-19
 * Time: 17:48
 */

namespace ZZG\Payload;


class Claim
{
    const ISSUER = 'iss';
    const SUBJECT = 'sub';
    const AUDIENCE = 'aud';
    const EXPIRATION_TIME = 'exp';
    const NOT_BEFORE = 'nbf';
    const ISSUED_AT = 'iat';
    const JWT_ID = 'jti';

    private $_object ;
    public function __construct(array $data = [])
    {
        $this->_object = new \stdClass();
        if (!empty($data)) {
            foreach ($data as $key=>$value) {
                $this->_setClaim($key,$value);
            }
        }
        if ($this->getIssuedAt() === null) {
            $this->setIssuedAt(time());
        }
        if ($this->getNotBefore() === null) {
            $this->setNotBefore(time());
        }
        if ($this->getExpirationTime() === null) {
            $this->setExpirationTime(-1);
        }
    }

    /**
     * 签发人
     * @param $value string
     * @return Claim
     *
     * The "iss" (issuer) claim identifies the principal that issued the
     * JWT.  The processing of this claim is generally application specific.
     * The "iss" value is a case-sensitive string containing a StringOrURI
     * value.  Use of this claim is OPTIONAL.
     */
    public function setIssuer($value){
        return $this->_setClaim(self::ISSUER,$value);
    }
    public function getIssuer(){
        return $this->get(self::ISSUER);
    }
    /**
     * 主题
     * @param $value string
     * @return Claim
     *
     * The "sub" (subject) claim identifies the principal that is the
     * subject of the JWT.  The claims in a JWT are normally statements
     * about the subject.  The subject value MUST either be scoped to be
     * locally unique in the context of the issuer or be globally unique.
     * The processing of this claim is generally application specific.  The
     * "sub" value is a case-sensitive string containing a StringOrURI
     * value.  Use of this claim is OPTIONAL.
     */
    public function setSubject($value) {
        return $this->_setClaim(self::SUBJECT,$value);
    }
    public function getSubject() {
        return $this->get(self::SUBJECT);
    }

    /**
     * 签发对象
     * @param $value string
     * @return Claim
     *
     * The "aud" (audience) claim identifies the recipients that the JWT is
     * intended for.  Each principal intended to process the JWT MUST
     * identify itself with a value in the audience claim.  If the principal
     * processing the claim does not identify itself with a value in the
     * "aud" claim when this claim is present, then the JWT MUST be
     * rejected.  In the general case, the "aud" value is an array of case-
     * sensitive strings, each containing a StringOrURI value.  In the
     * special case when the JWT has one audience, the "aud" value MAY be a
     * single case-sensitive string containing a StringOrURI value.  The
     * interpretation of audience values is generally application specific.
     * Use of this claim is OPTIONAL.
     */
    public function setAudience($value) {
        return $this->_setClaim(self::AUDIENCE,$value);
    }
    public function getAudience() {
        return $this->get(self::AUDIENCE);
    }

    /**
     * 过期时间
     * @param $value string
     * @return Claim
     *
     * The "exp" (expiration time) claim identifies the expiration time on
     * or after which the JWT MUST NOT be accepted for processing.  The
     * processing of the "exp" claim requires that the current date/time
     * MUST be before the expiration date/time listed in the "exp" claim.
     * Implementers MAY provide for some small leeway, usually no more than
     * a few minutes, to account for clock skew.  Its value MUST be a number
     * containing a NumericDate value.  Use of this claim is OPTIONAL.
     */
    public function setExpirationTime($value) {
        return $this->_setClaim(self::EXPIRATION_TIME,$value);
    }
    public function getExpirationTime() {
        return $this->get(self::EXPIRATION_TIME);
    }

    /**
     * jwt生效时间
     * @param $value string
     * @return Claim
     *
     * The "nbf" (not before) claim identifies the time before which the JWT
     * MUST NOT be accepted for processing.  The processing of the "nbf"
     * claim requires that the current date/time MUST be after or equal to
     * the not-before date/time listed in the "nbf" claim.  Implementers MAY
     * provide for some small leeway, usually no more than a few minutes, to
     * account for clock skew.  Its value MUST be a number containing a
     * NumericDate value.  Use of this claim is OPTIONAL.
     */
    public function setNotBefore($value) {
        return $this->_setClaim(self::NOT_BEFORE,$value);
    }

    public function getNotBefore() {
        return $this->get(self::NOT_BEFORE);
    }

    /**
     * jwt 签发时间
     * @param $value string
     * @return Claim
     *
     * The "iat" (issued at) claim identifies the time at which the JWT was
     * issued.  This claim can be used to determine the age of the JWT.  Its
     * value MUST be a number containing a NumericDate value.  Use of this
     * claim is OPTIONAL.
     */
    public function setIssuedAt($value) {
        return $this->_setClaim(self::ISSUED_AT,$value);
    }
    public function getIssuedAt() {
        return $this->get(self::ISSUED_AT);
    }

    /**
     * jwt ID
     * @param $value string
     *
     * @return Claim
     * The "jti" (JWT ID) claim provides a unique identifier for the JWT.
     * The identifier value MUST be assigned in a manner that ensures that
     * there is a negligible probability that the same value will be
     * accidentally assigned to a different data object; if the application
     * uses multiple issuers, collisions MUST be prevented among values
     * produced by different issuers as well.  The "jti" claim can be used
     * to prevent the JWT from being replayed.  The "jti" value is a case-
     * sensitive string.  Use of this claim is OPTIONAL.
     *
     */
    public function setJwtId($value) {
        return $this->_setClaim(self::JWT_ID,$value);
    }
    public function getJwtId() {
        return $this->get(self::JWT_ID);
    }

    public function setPublicClaim($name,$value){
        return $this->_setClaim($name,$value);
    }

    public function setPrivateClaim($name,$value) {
        return $this->_setClaim($name,$value);
    }

    public function toArray(){
        return (array)$this->_object;
    }
    private function _setClaim($name,$value) {
        $this->_object->{$name} = $value;
        return $this;
    }
    public function get($name)
    {
        return property_exists($this->_object,$name)?$this->_object->{$name}:null;
    }
}