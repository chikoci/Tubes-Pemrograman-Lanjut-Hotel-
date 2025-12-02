<?php
// Raw Expression untuk query mentah (misal: NOW())
class RawExpression {
    protected $value;
    
    public function __construct($value) { 
        $this->value = $value; 
    }
    
    public function getValue() { 
        return $this->value; 
    }
}

// Query Builder Class
class QueryBuilder {
    protected $db;
    protected $table;
    protected $selects = ['*'];
    protected $joins = [];
    protected $wheres = [];
    protected $orderBy = [];
    protected $limit;
    protected $offset;
    protected $bindings = [];
    protected $binding_counter = 0;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    protected function newQuery(): self {
        $this->selects = ['*'];
        $this->joins = [];
        $this->wheres = [];
        $this->orderBy = [];
        $this->limit = null;
        $this->offset = null;
        $this->bindings = [];
        $this->binding_counter = 0;
        return $this;
    }

    public function table(string $table): self {
        $this->newQuery(); 
        $this->table = $table;
        return $this;
    }
    
    public function raw(string $value): RawExpression {
        return new RawExpression($value);
    }

    public function select(array $columns = ['*']): self {
        $this->selects = $columns;
        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second): self {
        $this->joins[] = "JOIN $table ON $first $operator $second";
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self {
        $this->joins[] = "LEFT JOIN $table ON $first $operator $second";
        return $this;
    }
    
    public function where(string $column, string $operator, $value): self {
        $placeholder = $this->addBinding($value);
        $this->wheres[] = ['type' => 'AND', 'query' => "$column $operator $placeholder"];
        return $this;
    }

    public function orWhere(string $column, string $operator, $value): self {
        $placeholder = $this->addBinding($value);
        $this->wheres[] = ['type' => 'OR', 'query' => "$column $operator $placeholder"];
        return $this;
    }

    public function whereNull(string $column, string $type = 'AND'): self {
        $this->wheres[] = ['type' => $type, 'query' => "$column IS NULL"];
        return $this;
    }
    
    public function whereNotNull(string $column, string $type = 'AND'): self {
        $this->wheres[] = ['type' => $type, 'query' => "$column IS NOT NULL"];
        return $this;
    }
    
    public function whereIn(string $column, array $values, string $type = 'AND'): self {
        if (empty($values)) return $this;
        $placeholders = implode(',', array_map([$this, 'addBinding'], $values));
        $this->wheres[] = ['type' => $type, 'query' => "$column IN ($placeholders)"];
        return $this;
    }

    public function setBindingCounter(int $count): void {
        $this->binding_counter = $count;
    }
    
    public function getBindingCounter(): int {
        return $this->binding_counter;
    }
    
    public function whereGrouped(callable $callback, string $type = 'AND'): self {
        $query = new self($this->db);
        $query->setBindingCounter($this->binding_counter);
        
        $callback($query);
        
        if (!empty($query->wheres)) {
            $sql = $this->compileWheres($query->wheres, true);
            $this->wheres[] = ['type' => $type, 'query' => "($sql)"];
            $this->bindings = array_merge($this->bindings, $query->bindings);
            $this->binding_counter = $query->getBindingCounter(); 
        }
        return $this;
    }
    
    public function orderBy(string $column, string $direction = 'ASC'): self {
        $this->orderBy[] = "$column $direction";
        return $this;
    }
    
    public function limit(int $count): self {
        $this->limit = $count;
        return $this;
    }

    public function offset(int $count): self {
        $this->offset = $count;
        return $this;
    }
    
    protected function addBinding($value): string {
        $this->binding_counter++; 
        $placeholder = ':b' . $this->binding_counter; 
        $this->bindings[$placeholder] = $value;
        return $placeholder;
    }
    
    protected function compileWheres(array $wheres, bool $isGrouped = false): string {
        $sql = '';
        foreach ($wheres as $i => $where) {
            $prefix = ($i == 0 || $isGrouped && $i == 0) ? '' : $where['type'] . ' ';
            $sql .= $prefix . $where['query'] . ' ';
        }
        return trim($sql);
    }
    
    protected function buildSelectQuery(): string {
        $sql = "SELECT " . implode(', ', $this->selects) . " FROM " . $this->table;
        
        if (!empty($this->joins)) {
            $sql .= " " . implode(' ', $this->joins);
        }
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . $this->compileWheres($this->wheres);
        }
        if (!empty($this->orderBy)) {
            $sql .= " ORDER BY " . implode(', ', $this->orderBy);
        }
        if ($this->limit !== null) {
            $sql .= " LIMIT " . $this->limit;
        }
        if ($this->offset !== null) {
            $sql .= " OFFSET " . $this->offset;
        }
        
        return $sql;
    }
    
    public function get(): array {
        $stmt = $this->db->prepare($this->buildSelectQuery());
        $stmt->execute($this->bindings);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function first(): ?array {
        $this->limit(1);
        $stmt = $this->db->prepare($this->buildSelectQuery());
        $stmt->execute($this->bindings);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function count(string $column = '*'): int {
        $this->selects = ["COUNT($column) as total"];
        $this->orderBy = [];
        $this->limit = null;
        $this->offset = null;
        
        $stmt = $this->db->prepare($this->buildSelectQuery());
        $stmt->execute($this->bindings);
        return (int) $stmt->fetchColumn();
    }
    
    public function insert(array $data): bool {
        $columns = implode(', ', array_keys($data));
        $placeholders = [];
        $bindings = [];
        
        foreach ($data as $key => $value) {
            $placeholder = ':' . $key;
            $placeholders[] = $placeholder;
            $bindings[$placeholder] = ($value instanceof RawExpression) ? $value->getValue() : $value;
        }
        
        $sql = "INSERT INTO {$this->table} ($columns) VALUES (" . implode(', ', $placeholders) . ")";
        
        foreach ($bindings as $key => $value) {
            if ($value instanceof RawExpression) {
                $sql = str_replace($key, $value->getValue(), $sql);
                unset($bindings[$key]);
            }
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($bindings);
    }

    public function insertGetId(array $data): ?int {
        if ($this->insert($data)) {
            return (int) $this->db->lastInsertId();
        }
        return null;
    }
    
    public function update(array $data): bool {
        $setParts = [];
        $bindings = [];
        
        foreach ($data as $key => $value) {
            $placeholder = ':u_' . $key; 
            if ($value instanceof RawExpression) {
                $setParts[] = "$key = " . $value->getValue();
            } elseif ($value === null) {
                $setParts[] = "$key = NULL";
            } else {
                $setParts[] = "$key = $placeholder";
                $bindings[$placeholder] = $value;
            }
        }
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts);
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . $this->compileWheres($this->wheres);
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute(array_merge($bindings, $this->bindings));
    }
    
    public function delete(): bool {
        $sql = "DELETE FROM {$this->table}";
        
        if (!empty($this->wheres)) {
            $sql .= " WHERE " . $this->compileWheres($this->wheres);
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($this->bindings);
    }
}
?>
