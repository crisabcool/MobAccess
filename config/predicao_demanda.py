import sys
import json
import mysql.connector
import numpy as np
import pandas as pd
from sklearn.ensemble import RandomForestRegressor

def executar_analise():
    try:
        # 1. Conexão direta com o banco de dados do XAMPP local
        conn = mysql.connector.connect(
            host="127.0.0.1",
            port=3306,
            user="root",
            password="",
            database="mobaccess"
        )
        
        # 2. Leitura nativa usando cursor
        cursor = conn.cursor()
        cursor.execute("""
            SELECT dia_semana, is_feriado, distancia_metro_m, is_polo_saude, usuarios_historico_dia, demanda_real 
            FROM estatisticas_mobilidade
        """)
        linhas = cursor.fetchall()
        colunas = [desc[0] for desc in cursor.description]
        
        # Cria o DataFrame manualmente a partir dos dados do cursor
        df = pd.DataFrame(linhas, columns=colunas)
        
        cursor.close()
        conn.close()
        
        if df.empty:
            return {"status": "erro", "mensagem": "Base de dados estatísticos vazia."}

        # 3. Separação de atributos (X) e variável alvo (y)
        X = df[["dia_semana", "is_feriado", "distancia_metro_m", "is_polo_saude", "usuarios_historico_dia"]]
        y = df["demanda_real"]
        
        # 4. Treinamento do Modelo RandomForest do scikit-learn
        model = RandomForestRegressor(n_estimators=100, random_state=42)
        model.fit(X.values, y.values)

        # 5. Predição pontual se receber os 5 argumentos via terminal
        if len(sys.argv) == 6:
            args = [float(x) for x in sys.argv[1:6]]
            predicao = model.predict([args])[0]
            sugestao_frota = max(0, int(np.ceil(predicao)))

            return {
                "status": "sucesso",
                "scooters_recomendadas": sugestao_frota
            }
        else:
            return {"status": "erro", "mensagem": "Quantidade de argumentos incorreta fornecida via terminal."}

    except Exception as e:
        return {"status": "erro", "mensagem": str(e)}

if __name__ == "__main__":
    resultado = executar_analise()
    print(json.dumps(resultado))