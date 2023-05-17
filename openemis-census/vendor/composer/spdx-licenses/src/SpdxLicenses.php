<?php

/*
 * This file is part of composer/spdx-licenses.
 *
 * (c) Composer <https://github.com/composer>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Composer\Spdx;

class SpdxLicenses
{
    /** @var string */
    const LICENSES_FILE = 'spdx-licenses.json';

    /** @var string */
    const EXCEPTIONS_FILE = 'spdx-exceptions.json';

    /**
     * Contains all the licenses.
     *
     * The array is indexed by license identifiers, which contain
     * a numerically indexed array with license details.
     *
     *  [ license identifier =>
     *      [ 0 => full name (string), 1 => osi certified (bool) ]
     *    , ...
     *  ]
     *
     * @var array
     */
    private $licenses;

    /**
     * @var string
     */
    private $licensesExpression;

    /**
     * Contains all the license exceptions.
     *
     * The array is indexed by license exception identifiers, which contain
     * a numerically indexed array with license exception details.
     *
     *  [ exception identifier =>
     *      [ 0 => full name (string) ]
     *    , ...
     *  ]
     *
     * @var array
     */
    private $exceptions;

    /**
     * @var string
     */
    private $exceptionsExpression;

    public function __construct()
    {
        $this->loadLicenses();
        $this->loadExceptions();
    }

    /**
     * Returns license metadata by license identifier.
     *
     * This function adds a link to the full license text to the license metadata.
     * The array returned is in the form of:
     *
     *  [ 0 => full name (string), 1 => osi certified, 2 => link to license text (string) ]
     *
     * @param string $identifier
     *
     * @return array|null
     */
    public function getLicenseByIdentifier($identifier)
    {
        if (!isset($this->licenses[$identifier])) {
            return;
        }

        $license = $this->licenses[$identifier];
        $license[] = 'https://spdx.org/licenses/' . $identifier . '.html#licenseText';

        return $license;
    }

    /**
     * Returns license exception metadata by license exception identifier.
     *
     * This function adds a link to the full license exception text to the license exception metadata.
     * The array returned is in the form of:
     *
     *  [ 0 => full name (string), 1 => link to license text (string) ]
     *
     * @param string $identifier
     *
     * @return array|null
     */
    public function getExceptionByIdentifier($identifier)
    {
        if (!isset($this->exceptions[$identifier])) {
            return;
        }

        $license = $this->exceptions[$identifier];
        $license[] = 'https://spdx.org/licenses/' . $identifier . '.html#licenseExceptionText';

        return $license;
    }

    /**
     * Returns the short identifier of a license (or license exception) by full name.
     *
     * @param string $name
     *
     * @return string|null
     */
    public function getIdentifierByName($name)
    {
        foreach ($this->licenses as $identifier => $licenseData) {
            if ($licenseData[0] === $name) {
                return $identifier;
            }
        }

        foreach ($this->exceptions as $identifier => $licenseData) {
            if ($licenseData[0] === $name) {
                return $identifier;
            }
        }
    }

    /**
     * Returns the OSI Approved status for a license by identifier.
     *
     * @param string $identifier
     *
     * @return bool
     */
    public function isOsiApprovedByIdentifier($identifier)
    {
        return $this->licenses[$identifier][1];
    }

    /**
     * @param array|string $license
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function validate($license)
    {
        if (is_array($license)) {
            $count = count($license);
            if ($count !== count(array_filter($license, 'is_string'))) {
                throw new \InvalidArgumentException('Array of strings expected.');
            }
            $license = $count > 1  ? '(' . implode(' OR ', $license) . ')' : (string) reset($license);
        }

        if (!is_string($license)) {
            throw new \InvalidArgumentException(sprintf(
                'Array or String expected, %s given.',
                gettype($license)
            ));
        }

        return $this->isValidLicenseString($license);
    }

    /**
     * @return string
     */
    public static function getResourcesDir()
    {
        return dirname(__DIR__) . '/res';
    }

    private function loadLicenses()
    {
        if (null === $this->licenses) {
            $json = file_get_contents(self::getResourcesDir() . '/' . self::LICENSES_FILE);
            $this->licenses = json_decode($json, true);
        }
    }

    private function loadExceptions()
    {
        if (null === $this->exceptions) {
            $json = file_get_contents(self::getResourcesDir() . '/' . self::EXCEPTIONS_FILE);
            $this->exceptions = json_decode($json, true);
        }
    }

    /**
     * @return string
     */
    private function getLicensesExpression()
    {
        if (null === $this->licensesExpression) {
            $licenses = array_map('preg_quote', array_keys($this->licenses));
            rsort($licenses);
            $licenses = implode('|', $licenses);
            $this->licensesExpression = $licenses;
        }

        return $this->licensesExpression;
    }

    /**
     * @return string
     */
    private function getExceptionsExpression()
    {
        if (null === $this->exceptionsExpression) {
            $exceptions = array_map('preg_quote', array_keys($this->exceptions));
            rsort($exceptions);
            $exceptions = implode('|', $exceptions);
            $this->exceptionsExpression = $exceptions;
        }

        return $this->exceptionsExpression;
    }

    /**
     * @param string $license
     *
     * @throws \RuntimeException
     *
     * @return bool
     */
    private function isValidLicenseString($license)
    {
        if (isset($this->licenses[$license])) {
            return true;
        }

        $licenses = $this->getLicensesExpression();
        $exceptions = $this->getExceptionsExpression();

        $regex = <<<REGEX
{
(?(DEFINE)
    # idstring: 1*( ALPHA / DIGIT / - / . )
    (?<idstring>[\pL\pN.-]{1,})

    # license-id: taken from list
    (?<licenseid>${licenses})

    # license-exception-id: taken from list
    (?<licenseexceptionid>${exceptions})

    # license-ref: [DocumentRef-1*(idstring):]LicenseRef-1*(idstring)
    (?<licenseref>(?:DocumentRef-(?&idstring):)?LicenseRef-(?&idstring))

    # simple-expresssion: license-id / license-id+ / license-ref
    (?<simple_expression>(?&licenseid)\+? | (?&licenseid) | (?&licenseref))

    # compound-expression: 1*(
    #   simple-expression /
    #   simple-expression WITH license-exception-id /
    #   compound-expression AND compound-expression /
    #   compound-expression OR compound-expression
    # ) / ( compound-expression ) )
    (?<compound_head>
        (?&simple_expression) ( \s+ (?:with|WITH) \s+ (?&licenseexceptionid))?
            | \( \s* (?&compound_expression) \s* \)
    )
    (?<compound_expression>
        (?&compound_head) (?: \s+ (?:and|AND|or|OR) \s+ (?&compound_expression))?
    )

    # license-expression: 1*1(simple-expression / compound-expression)
    (?<license_expression>(?&compound_expression) | (?&simple_expression))
) # end of define

^(NONE | NOASSERTION | (?&license_expression))$
}x
REGEX;

        $match = preg_match($regex, $license);

        if (0 === $match) {
            return false;
        }

        if (false === $match) {
            throw new \RuntimeException('Regex failed to compile/run.');
        }

        return true;
    }
}
