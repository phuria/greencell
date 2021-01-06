Word Generator
---

Installation:

```bash
composer install
```

Usage:
```bash
./generate [options] [--] [<words>]
```  
    
Arguments:
  - `<words>` (number of generated words)

Options:
  - `-f`, `--force` (disable security)
  - `--max-word-length=MAX-WORD-LENGTH` (max length of word)
  - `--min-word-length=MIN-WORD-LENGTH` (min length of word)
  - `--now=NOW` (current time, any supported format by php)

