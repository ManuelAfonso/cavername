# cavername
Cavername é um script PHP que facilita a criação de páginas Web. Normalmente, as páginas de um site têm vários elementos comuns e torna-se pouco prático fazer a manutenção se for preciso repetir muitas vezes o mesmo elemento, como é o caso do menu principal, cabeçalho, rodapé e outras áreas comuns a um conjunto de "páginas".

Um parâmetro do url identifica o conteúdo que vai ser escrito na zona principal, os conteúdos a colocar noutras zonas e o esquema da página. Tudo isto é definido no ficheiro cavername.db. O estilo e o layout estão definidos num conjunto de ficheiros que definem um "tema".

Normalmente os conteúdos encontram-se definidos em ficheiros mas também podem ser obtidos como resultado de "funções".

As funcionalidades mais importantes estão centradas na apresentação de textos com paginação por capítulos ou quebras de página, na divisão por colunas, apresentação bilingue e na combinação das anteriores, usando como fonte um único ficheiro para cada conteúdo.

Exemplo que serviu ao desenvolvimento do script:
* http://movimentohumanista.canal139.com/
* apresentação bilingue: http://movimentohumanista.canal139.com/?a=materiais/pv1969_bilingue&mainzone-p=1
* paginação por capítulos: http://movimentohumanista.canal139.com/?a=materiais/documento
* paginação por quebras de página e colunas: http://movimentohumanista.canal139.com/?a=materiais/pv1999
