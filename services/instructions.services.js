export class InstructionsService {
    constructor ($axios, $toast) {
      this.$axios = $axios
      this.$toast = $toast
    }
   
    computedFilter(filter)
    {
        let url = '';

        if (Object.keys(filter).length > 0)
        {
            Object.keys(filter).forEach(el => {
                if (filter[el] != null || filter[el] != "" || filter[el] > 0)
                    url += `&${el}=${filter[el]}`
            })
        }

        return url
    }

    computedUrl(options)
    {
        let url = ''

        if (options.sortBy.length <= 0)
            url = `api/v1/instruction?pageNumber=${options.page}&pageSize=${options.itemsPerPage}`;
        else
          url = `api/v1/instruction?pageNumber=${options.page}&pageSize=${options.itemsPerPage}&sortBy=${options.sortBy[0]} ${options.sortDesc[0] ? "desc" : ""}`;
        
        return url
    }

    async view (options, filter)
    {
      if ($nuxt.$permissions.can('view', 'instruction'))
      {
        let data;
        
        try
        {
            let url = this.computedUrl(options);
            let filterUrl = "";
            if (filter) filterUrl = this.computedFilter(filter);

            await this.$axios.get(url + filterUrl).then(response => {
                    data = response.data
                }
            );

            return data;
        }
        catch (e)
        {
            this.$toast.error("Ошибка во время загрузки инструкций.");
        }
      }
      else
      {
        this.$toast.error("У вас нет прав на просмотр инструкций.");
      }
    }

    async getById (itemId)
    {
      if ($nuxt.$permissions.can('view', 'instruction'))
      {
        let data;
        
        try
        {
            await this.$axios.get(`api/v1/instruction/${itemId}`).then(response => {
                    data = response.data
                }
            );
            return data;
        }
        catch (e)
        {
            this.$toast.error("Ошибка во время загрузки инструкций.");
        }
      }
      else
      {
        this.$toast.error("У вас нет прав на чтение инструкций.");
      }
    }
  }